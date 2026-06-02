<!-- CREATE/EDIT ORDER MODAL -->
<div class="modal-overlay" id="custModal" onclick="closeModalOutside(event, 'custModal')">
    <div class="modal-box" style="max-width:540px">
        <div class="modal-header">
            <div class="modal-header-icon"><i class="fas fa-plus"></i></div>
            <div class="modal-title">
                <h3 id="modalBoxTitle">Buat Order Baru</h3>
                <p>Formulir pembuatan dan pembaruan order transaksi</p>
            </div>
            <button class="modal-close" onclick="closeModal('custModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="orderForm">
            <input type="hidden" id="orderId" name="id">
            <div class="modal-body">
                <x-form.select 
                    formGroup 
                    label="Pelanggan" 
                    id="orderCustomer" 
                    name="customer_id" 
                    required
                >
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                    @endforeach
                </x-form.select>

                <x-form.select 
                    formGroup 
                    label="Outlet" 
                    id="orderOutlet" 
                    name="outlet_id" 
                    required
                >
                    <option value="">-- Pilih Outlet --</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select 
                    formGroup 
                    label="Layanan" 
                    id="orderServiceType" 
                    name="service_type" 
                    required
                >
                    <option value="Cuci Setrika">Cuci Setrika (Rp 8.000 / kg)</option>
                    <option value="Cuci Kering">Cuci Kering (Rp 7.000 / kg)</option>
                    <option value="Setrika Saja">Setrika Saja (Rp 5.000 / kg)</option>
                    <option value="Cuci Bed Cover">Cuci Bed Cover (Rp 25.000 / kg)</option>
                </x-form.select>

                <div class="modal-grid">
                    <x-form.input 
                        formGroup 
                        label="Berat / Qty" 
                        type="number" 
                        step="0.01" 
                        min="0.01" 
                        id="orderWeight" 
                        name="weight" 
                        value="1" 
                        required 
                    />
                    
                    <x-form.input 
                        formGroup 
                        label="Harga Satuan" 
                        type="number" 
                        min="0" 
                        id="orderPricePerUnit" 
                        name="price_per_unit" 
                        value="8000" 
                        required 
                    />
                </div>

                <div class="modal-grid">
                    <x-form.select 
                        formGroup 
                        label="Status Order" 
                        id="orderStatusSelect" 
                        name="order_status"
                    >
                        <option value="Baru">Baru</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Diambil">Diambil</option>
                    </x-form.select>

                    <x-form.select 
                        formGroup 
                        label="Pembayaran" 
                        id="orderPaymentStatusSelect" 
                        name="payment_status"
                    >
                        <option value="Belum">Belum Bayar</option>
                        <option value="Lunas">Lunas</option>
                    </x-form.select>
                </div>

                <x-form.select 
                    formGroup 
                    label="Metode Pembayaran" 
                    id="orderPaymentMethod" 
                    name="payment_method"
                >
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer">Transfer Bank</option>
                    <option value="QRIS">QRIS</option>
                </x-form.select>

                <x-form.textarea 
                    formGroup 
                    label="Catatan Tambahan" 
                    id="orderNotes" 
                    name="notes" 
                    rows="2" 
                    placeholder="Catatan khusus dari pelanggan..." 
                />
            </div>
            <div class="modal-footer">
                <x-form.button type="button" variant="outline" onclick="closeModal('custModal')">Batal</x-form.button>
                <x-form.button type="submit" variant="primary">Simpan Transaksi</x-form.button>
            </div>
        </form>
    </div>
</div>
