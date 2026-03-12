<?php

use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk fungsi jam() dan pilihan_prodi()

if (!function_exists('wrap_words')) {
    function wrap_words($string, $word_limit) {
        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit));
    }
}

if (!function_exists('dash_words')) {
    function dash_words($string) {
        return str_replace(" ", "-", $string);
    }
}

if (!function_exists('slash_words')) {
    function slash_words($string) {
        return str_replace("-", "/", $string);
    }
}

if (!function_exists('getBulan')) {
    function getBulan($bln) {
        switch ($bln){
            case 1: return "January";
            case 2: return "February";
            case 3: return "March";
            case 4: return "April";
            case 5: return "May";
            case 6: return "June";
            case 7: return "July";
            case 8: return "August";
            case 9: return "September";
            case 10: return "October";
            case 11: return "November";
            case 12: return "December";
            default: return "";
        }
    }
}

if (!function_exists('datetr')) {
    function datetr($tgl) {
        $tanggal = substr($tgl, 8, 2);
        $bulan = getBulan(substr($tgl, 5, 2)); // Hapus $this-> karena ini bukan di dalam class
        $tahun = substr($tgl, 0, 4);
        return $tanggal . ' ' . $bulan . ' ' . $tahun;       
    }
}

if (!function_exists('datetostring')) {
    function datetostring($days) {
        $year = $days / 365;
        $rem = $days % 365;
        $mos = $rem / 30;
        $dys = $rem % 30;
        $string = (int)$year . " yrs " . (int)$mos . " mos " . $dys . " dys";
        return $string;
    }
}

if (!function_exists('jam')) {
    function jam($id) {
        // Konversi dari CI get_instance() ke Laravel DB Facade
        $rs = DB::table('master_jam')->where('id', $id)->first();
        if ($rs) {
            return $rs->nama_sesi;
        } else {
            return '-- Pilih Sesi --';
        }
    }
}

if (!function_exists('pilihan_prodi')) {
    function pilihan_prodi($id = '') {
        // Konversi dari CI get_instance() ke Laravel DB Facade
        $rs = DB::table('program_studi')->where('id', $id)->first();
        if ($rs) {
            return $rs->nama_jurusan;
        } else {
            return '-';
        }
    }
}

if (!function_exists('nmutu')) {
    function nmutu($nilai = '') {
        $val = 'E';
        if (($nilai >= 85) && ($nilai <= 100)) {
            $val = 'A';
        } elseif (($nilai >= 79) && ($nilai < 84.999)) {
            $val = 'AB';
        } elseif (($nilai >= 73) && ($nilai < 78.999)) {
            $val = 'B';
        } elseif (($nilai >= 67) && ($nilai < 72.999)) {
            $val = 'BC';
        } elseif (($nilai >= 61) && ($nilai < 66.999)) {
            $val = 'C';
        } elseif (($nilai >= 55) && ($nilai < 60.999)) {
            $val = 'CD';
        } elseif (($nilai >= 45) && ($nilai < 54.999)) {
            $val = 'D';
        } else {
            $val = 'E';
        }
        return $val;
    }
}

if (!function_exists('nbobot')) {
    function nbobot($nilai = '') {
        $val = 0;
        if ($nilai == 'A') {
            $val = 4;
        } elseif ($nilai == 'AB') {
            $val = 3.5;
        } elseif ($nilai == 'B'){
            $val = 3;
        } elseif ($nilai == 'BC') {
            $val = 2.5;
        } elseif ($nilai == 'C') {
            $val = 2;
        } elseif ($nilai == 'CD') {
            $val = 1.5;
        } elseif ($nilai == 'D') {
            $val = 1;
        } elseif ($nilai == 'E') {
            $val = 0;
        }
        return $val;
    }
}

if (!function_exists('sksbatas')) {
    function sksbatas($ips = '') {
        $val = 12;
        if (($ips >= 3.51) && ($ips <= 4)) {
            $val = 24;
        } elseif (($ips >= 2.51) && ($ips <= 3.5)) {
            $val = 22;
        } elseif (($ips >= 2) && ($ips <= 2.5)) {
            $val = 20;
        } elseif (($ips >= 1.51) && ($ips <= 1.99)) {
            $val = 16;
        } elseif ($ips <= 1.50 && $ips > 0) {
            $val = 12;
        } elseif ($ips == 0) {
            $val = 24;
        }
        return $val;
    }
}