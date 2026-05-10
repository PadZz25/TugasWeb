<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotaPenjualanResource\Pages;
use App\Models\NotaPenjualan;
use App\Models\KatalogBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;

class NotaPenjualanResource extends Resource
{
    protected static ?string $model = NotaPenjualan::class;

    // Ganti icon biar bentuk keranjang belanja
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart'; 
    protected static ?string $modelLabel = 'Transaksi Kasir';
    protected static ?string $pluralModelLabel = 'Transaksi Kasir';
    protected static ?string $navigationGroup = 'Transaksi & keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // KOTAK 1: INFORMASI NOTA
                Forms\Components\Section::make('Informasi Nota')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_nota')
                            ->label('Nomor Nota')
                            // Otomatis bikin nomor unik dari tahun-bulan-tanggal-jam-menit-detik
                            ->default('INV-' . date('YmdHis')) 
                            ->readOnly()
                            ->required(),

                        // Sembunyiin ID Kasir tapi tetep disimpen ke database
                        Forms\Components\Hidden::make('id_karyawan')
                            ->default(fn () => Auth::user()->id_karyawan) // Ambil kolom id_karyawan langsung dari user
                            ->required(),

                        // Tampilan nama kasir yang lagi login (biar keren)
                        Forms\Components\Placeholder::make('nama_kasir')
                            ->label('Kasir yang Bertugas')
                            ->content(fn () => Auth::user()->nama),

                        Forms\Components\Select::make('id_pelanggan')
                            ->relationship('pelanggan', 'nama')
                            ->label(fn (Get $get) => $get('metode_bayar') === 'hutang' ? 'Pelanggan (Wajib Diisi!)' : 'Pelanggan (Opsional)')
                            ->searchable()
                            ->placeholder('Pilih atau tambah baru...')
                            ->required(fn (Get $get) => $get('metode_bayar') === 'hutang')
                            // INI DIA MAGIC-NYA: Bikin form pop-up buat nambah pelanggan baru
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Pelanggan Baru')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon (Opsional)')
                                    ->tel()
                                    ->maxLength(255),
                                    
                                // Total hutang gak usah dimasukin sini, biarin otomatis 0 dari database-nya
                            ]),

                        Forms\Components\DateTimePicker::make('tanggal')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('metode_bayar')
                            ->label('Metode Bayar')
                            ->options([
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer',
                                'hutang' => 'Hutang',
                            ])
                            ->default('tunai')
                            ->live() // KUNCI PENTING: Biar ngasih tau kolom lain kalau dia lagi diubah
                            ->required(),

                        Forms\Components\Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'lunas' => 'Lunas',
                                'belum_lunas' => 'Belum Lunas',
                            ])
                            ->default('lunas')
                            ->required(),
                    ])->columns(3),

                // KOTAK 2: DAFTAR BELANJAAN (REPEATER)
                Forms\Components\Section::make('Daftar Belanjaan')
                    ->schema([
                        Forms\Components\Repeater::make('item') // Harus sama dengan nama relasi di Model Nota
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('id_barang')
                                    ->relationship('barang', 'nama')
                                    ->label('Pilih Barang / Scan Barcode') // Ubah labelnya biar kasir ngeh
                                    // INI MAGIC-NYA: Masukin nama kolomnya dalam format array
                                    ->searchable(['nama', 'barcode']) 
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems() // Fitur kerenmu tetep aman
                                    ->live() // Nyalain radar "Reactivity"
                                    // Begitu barang dipilih, langsung tarik harganya dari database
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $barang = KatalogBarang::find($state);
                                        if ($barang) {
                                            $set('harga_jual_saat_itu', $barang->harga_jual);
                                            $set('hpp_saat_itu', $barang->harga_beli);
                                            
                                            // Hitung subtotal sementara
                                            $jumlah = $get('jumlah') ?? 1;
                                            $set('subtotal', $barang->harga_jual * $jumlah);
                                        }
                                    }),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    // Begitu jumlah diganti, update subtotal per barang
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $harga = $get('harga_jual_saat_itu') ?? 0;
                                        $set('subtotal', $harga * $state);
                                    }),

                                Forms\Components\TextInput::make('harga_jual_saat_itu')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->required(),

                                Forms\Components\Hidden::make('hpp_saat_itu'),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->live()
                            // Begitu ada item nambah/berubah, langsung hitung Total Belanja Keseluruhan!
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('item') ?? [];
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += (float) ($item['subtotal'] ?? 0);
                                }
                                
                                $set('total_belanja', $total);
                                
                                $diskon = (float) ($get('diskon') ?? 0);
                                $set('total_akhir', $total - $diskon);
                            }),
                    ]),

                // KOTAK 3: TOTALAN & KASIR
                Forms\Components\Section::make('Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('total_belanja')
                            ->label('Total Belanja')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->default(0),

                        Forms\Components\TextInput::make('diskon')
                            ->label('Diskon / Potongan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            // Hitung ulang kalau kasir ngasih diskon
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $total = (float) ($get('total_belanja') ?? 0);
                                $set('total_akhir', $total - (float) $state);
                            }),

                        Forms\Components\TextInput::make('total_akhir')
                            ->label('TOTAL YANG HARUS DIBAYAR')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->default(0)
                            ->extraInputAttributes(['style' => 'font-weight: bold; font-size: 1.2rem; color: #10b981;']),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_nota')
                    ->label('No. Nota')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('Kasir')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->default('Umum')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_akhir')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status_bayar')
                    ->label('Status')
                    ->colors([
                        'danger' => 'belum_lunas',
                        'success' => 'lunas',
                    ]),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                \Filament\Tables\Actions\Action::make('cetak_struk')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => route('cetak.struk', $record->id_nota))
                    ->openUrlInNewTab(), // Buka di tab baru biar dashboard gak ketutup
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotaPenjualans::route('/'),
            'create' => Pages\CreateNotaPenjualan::route('/create'),
            'edit' => Pages\EditNotaPenjualan::route('/{record}/edit'),
        ];
    }
}