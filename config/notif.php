<?php
function kirim_notif($conn, $id_user, $judul, $pesan, $icon = 'bell') {
    $judul = mysqli_real_escape_string($conn, $judul);
    $pesan = mysqli_real_escape_string($conn, $pesan);
    $icon  = mysqli_real_escape_string($conn, $icon);
    mysqli_query($conn, "INSERT INTO notifikasi (id_user, judul, pesan, icon) VALUES ($id_user, '$judul', '$pesan', '$icon')");
}

function get_notif($conn, $id_user, $limit = 10) {
    return mysqli_query($conn, "SELECT * FROM notifikasi WHERE id_user=$id_user ORDER BY created_at DESC LIMIT $limit");
}

function count_unread($conn, $id_user) {
    return mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM notifikasi WHERE id_user=$id_user AND is_read=0"))['total'];
}

function mark_all_read($conn, $id_user) {
    mysqli_query($conn, "UPDATE notifikasi SET is_read=1 WHERE id_user=$id_user");
}
