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
                <div class="form-group">
                    <label for="orderCustomer">Pelanggan <span class="text-danger">*</span></label>
                    <select class="form-control" id="orderCustomer" name="customer_id" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="orderOutlet">Outlet <span class="text-danger">*</span></label>
                    <select class="form-control" id="orderOutlet" name="outlet_id" required>
                        <option value="">-- Pilih Outlet --</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="orderServiceType">Layanan <span class="text-danger">*</span></label>
                    <select class="form-control" id="orderServiceType" name="service_type" required>
                        <option value="Cuci Setrika">Cuci Setrika (Rp 8.000 / kg)</option>
                        <option value="Cuci Kering">Cuci Kering (Rp 7.000 / kg)</option>
                        <option value="Setrika Saja">Setrika Saja (Rp 5.000 / kg)</option>
                        <option value="Cuci Bed Cover">Cuci Bed Cover (Rp 25.000 / kg)</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="modal-grid">
                    <div class="form-group">
                        <label for="orderWeight">Berat / Qty <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="orderWeight" name="weight" value="1" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="orderPricePerUnit">Harga Satuan <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="orderPricePerUnit" name="price_per_unit" value="8000" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-grid">
                    <div class="form-group">
                        <label for="orderStatusSelect">Status Order</label>
                        <select class="form-control" id="orderStatusSelect" name="order_status">
                            <option value="Baru">Baru</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Diambil">Diambil</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="orderPaymentStatusSelect">Pembayaran</label>
                        <select class="form-control" id="orderPaymentStatusSelect" name="payment_status">
                            <option value="Belum">Belum Bayar</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="orderPaymentMethod">Metode Pembayaran</label>
                    <select class="form-control" id="orderPaymentMethod" name="payment_method">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="orderNotes">Catatan Tambahan</label>
                    <textarea class="form-control" id="orderNotes" name="notes" rows="2" placeholder="Catatan khusus dari pelanggan..."></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-outline" onclick="closeModal('custModal')">Batal</button>
                <button type="submit" class="modal-btn modal-btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>
