// Kalkulasi total biaya booking secara real-time
function hitungTotal(paketHarga, kendaraanHarga) {
    const idPaket = document.getElementById('sel_paket')?.value;
    const idKendaraan = document.getElementById('sel_kendaraan')?.value;
    const jumlah = parseInt(document.getElementById('jumlah_orang')?.value) || 0;

    const hargaPaket = idPaket ? (paketHarga[idPaket] || 0) : 0;
    const hargaKendaraan = idKendaraan ? (kendaraanHarga[idKendaraan] || 0) : 0;
    const total = (hargaPaket * jumlah) + hargaKendaraan;

    const displayTotal = document.getElementById('display_total');
    const inputTotal = document.getElementById('total_biaya');
    const rincian = document.getElementById('rincian');

    if (displayTotal) displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    if (inputTotal) inputTotal.value = Math.round(total);

    if (rincian && idPaket && idKendaraan) {
        rincian.innerHTML = `Paket: Rp ${(hargaPaket * jumlah).toLocaleString('id-ID')}<br>Kendaraan: Rp ${hargaKendaraan.toLocaleString('id-ID')}`;
    }
}

function filterPaket(idWisata) {
    window.location.href = 'booking.php' + (idWisata ? '?id_wisata=' + idWisata : '');
}
