<?php
class Pages extends CI_Controller
{
    var $arrayRuangan;

	function __construct(){
	    parent::__construct();
	}

	public function home(){
        $this->load->view('template/header');
        $this->load->view('pages/hompage');
        $this->load->view('template/footer');
	}

	public function jadwal(){
        $this->load->view('template/header');
        $this->load->view('pages/jadwal');
        $this->load->view('template/footer');
    }

    private function readfile(){
        //Di run di google chrome
//pastiin ada Testcase.txt. Kalo beda directory, edit-edit aja


//echo "Coba buat Struktur Data!<br><br>";

//nama file
        $name = "input.txt"; // nama file

//open file
        $file = fopen($name,"r");

//delete "Ruangan"
        $huruf = fread($file,9);

//variabel
        $arrayFile = array();

//start scanning ruang
        while (!feof($file)) {
            $huruf = fread($file,1);

            //take kata
            $kata = "";
            while (($huruf!==";")and($huruf!=="\n")and(!feof($file))) {
                $kata = $kata . $huruf;
                $huruf = fread($file,1);
            }

            //cek termiasi ruangan (kalo enter, berarti udh ganti ke list matkul)
            $testing = strlen($kata);
            if ($testing==1)
                break;

            //proses constraint
            //print("$kata<br>");
            array_push($arrayFile,$kata);
        }

        $jmlRuangan = count($arrayFile) / 4;

//echo $jmlRuangan . "<br>";

//delete "Jadwal"
        $huruf = fread($file,8);

//start scanning matkul
        while (!feof($file)) {
            $huruf = fread($file,1);

            //take kata
            $kata = "";
            while (($huruf!==";")and($huruf!=="\n")and(!feof($file))) {
                $kata = $kata . $huruf;
                $huruf = fread($file,1);
            }

            //proses constraint
            //print("$kata<br>");
            array_push($arrayFile,$kata);
        }

        $jmlMatkul = (count($arrayFile) - ($jmlRuangan*4)) / 6;

//echo $jmlMatkul . "<br>";

//close file
        fclose($file);

//echo "Beres baca file!!<br>";

//echo count($arrayFile) . "<br>";

//semua hal yg berhubungan dengan file sudah ditampung di array file


        /*
        //nyoba aja
        for ($i=1;$i<=$jmlRuangan;$i++) {
            echo strlen($arrayFile[($i-1)*4+3]) . "<br>";
            echo substr($arrayFile[($i-1)*4+3],0,1)+5 . "<br>";
        }
        */

//buat Struktur Data
//Dibuat array index untuk nama ruangan dan nama matkul

        $indexRuangan = array();
        for ($i=0;$i<$jmlRuangan;$i++) {
            array_push($indexRuangan,$arrayFile[$i*4]);
        }

//echo $indexRuangan[2] . "<br>";

        $indexMatkul = array();
        for ($i=0;$i<$jmlMatkul;$i++) {
            array_push($indexMatkul,$arrayFile[$jmlRuangan*4+$i*6]);
        }

//echo $indexMatkul[2] . "<br>";

//fungsi untuk membantu
        function getIndex($hari,$waktu)
//mengembalikan nilai index pada array untuk kode hari dan waktu start tertentu
        {
            return(($hari-1)*11+($waktu-7));
        }

        /*
        INDEX INDEX INDEX INDEX : Penjelasan tentang index
        $indexRuangan sama $indexMatkul untuk memudahkan dalam indexing
        KALO MAU TAU index sekian itu apa ruangannya atau apa matkulnya, tinggal akses arraynya : $indexSesuatu[sesuatu]
        KALO MAU TAU suatu nama matkul atau suatu nama ruangan itu ada di index berapa, tinggal search : array_search("Sesuatu",$indexSesuatu)

        STRUKTUR DATA STRUKTUR DATA STRUKTUR DATA : Penjelasan tentang struktur data singkat
        Jadi, dibuat array yang isinya $jmlRuangan Elemen (step 1)
        Elemen tersebut adalah array yang isinya $jmlMatkul+1 Elemen (step 2)
                elemen terakhir berisi array of boolean (yang dipake boolean ke 0 aja, boolean ke 1 bodo amat) contraint keberadaan ruangan itu
        Elemen tersebut adalah array yang isinya 55 (1 hari ada 11 jam kemungkinan) elemen (step 3)
        Elemen tersebut adalah array yang isinya 2 boolean (step 4)
        Boolean ke-0 menunjukkan apakah lokasi dan waktu tersebut diperbolehkan untuk matkul tersebut
        (true berarti boleh, false berarti gaboleh (ga ada slot))
        Boolean ke-1 menunjukkan slot yang ditempatin, INI YANG HARUS DIPINDAH-PINDAH.
        (true berarti ditempatin di situ, false berarti g diisi)

        4th dimensional array!!! wakakakak
        Kenapa boolean, karena mindah2in lebih gampang daripada kalo dibuat string (menurut gw)
        Ngabisis banyak bet space?? Ngabisin banyak waktu?? Keknya bukan kiteria penilaian

        */

//buat array step 1
        $this->arrayRuangan = array();
        for ($i=0;$i<$jmlRuangan;$i++)
            array_push($this->arrayRuangan,array());

//buat array step 2
        for ($i=0;$i<$jmlRuangan;$i++)
            for ($j=0;$j<$jmlMatkul+1;$j++)
                array_push($this->arrayRuangan[$i],array());

//buat array step 3
        for ($i=0;$i<$jmlRuangan;$i++)
            for ($j=0;$j<$jmlMatkul+1;$j++)
                for ($k=0;$k<55;$k++)
                    array_push($this->arrayRuangan[$i][$j],array());

//buat array step 4
        for ($i=0;$i<$jmlRuangan;$i++)
            for ($j=0;$j<$jmlMatkul+1;$j++)
                for ($k=0;$k<55;$k++)
                    array_push($this->arrayRuangan[$i][$j][$k],array());

//inisialisasi
//semua diisi 0 (false)
        for ($i=0;$i<$jmlRuangan;$i++)
            for ($j=0;$j<$jmlMatkul+1;$j++)
                for ($k=0;$k<55;$k++) {
                    $this->arrayRuangan[$i][$j][$k][0] = 0; $this->arrayRuangan[$i][$j][$k][1] = 0; }


//array selesai dibuat dan diinisialisasi!!!


//masukkin constraint Ruangan
        for ($i=0;$i<$jmlRuangan;$i++) {
            $waktu = $arrayFile[$i*4+1];
            $waktuAkhir = $arrayFile[$i*4+2];
            $durasi = $waktuAkhir-$waktu;
            //echo $durasi . "<br>";
            $listHari = $arrayFile[$i*4+3];
            $availableDays = strlen($listHari) / 2;
            for ($j=0;$j<$availableDays;$j++) {
                $hari = substr($listHari,$j*2,1);
                for ($k=0;$k<$durasi;$k++)
                    $this->arrayRuangan[$i][$jmlMatkul][getIndex($hari,$waktu)+$k][0] = 1;
            }
        }

//echo $this->arrayRuangan[1][8][54][0]&&$this->arrayRuangan[1][8][6][0] . "<br>";
//echo $this->arrayRuangan[1][8][54][0]||$this->arrayRuangan[1][8][6][0] . "<br>";


//masukkan slot yang dibolehin (boolean ke-0)
        for ($i=0;$i<$jmlMatkul;$i++) {
            $ruangan = $arrayFile[$jmlRuangan*4+$i*6+1];
            $waktu = $arrayFile[$jmlRuangan*4+$i*6+2];
            $waktuAkhir = $arrayFile[$jmlRuangan*4+$i*6+3];
            $durasi = $waktuAkhir-$waktu;
            $listHari = $arrayFile[$jmlRuangan*4+$i*6+5];
            $availableDays = strlen($listHari) / 2;
            for ($j=0;$j<$availableDays;$j++) {
                $hari = substr($listHari,$j*2,1);
                if ($ruangan=="-") {
                    for ($k=0;$k<$durasi;$k++)
                        for ($l=0;$l<$jmlRuangan;$l++)
                            if ($this->arrayRuangan[$l][$jmlMatkul][getIndex($hari,$waktu)+$k][0])
                                $this->arrayRuangan[$l][$i][getIndex($hari,$waktu)+$k][0] = 1;
                } else {
                    $idxRuang = array_search($ruangan,$indexRuangan);
                    for ($k=0;$k<$durasi;$k++)
                        if ($this->arrayRuangan[$idxRuang][$jmlMatkul][getIndex($hari,$waktu)+$k][0])
                            $this->arrayRuangan[$idxRuang][$i][getIndex($hari,$waktu)+$k][0] = 1;
                }
            }
        }

//echo $this->arrayRuangan[0][0][44][0] . "<br>";
//echo $this->arrayRuangan[0][0][48][0] . "<br>";
//echo $this->arrayRuangan[0][0][49][0] . "<br>";
//echo $this->arrayRuangan[2][0][44][0] . "<br>";
//echo $this->arrayRuangan[3][1][26][0] . "<br>";


//nah, sekarang statusnya masih kosong

//Karena local search itu complete, jadi diisi yak (ini bukan "random", ini gw pepetin aja di ruang dan waktu paling awal)
        for ($i=0;$i<$jmlMatkul;$i++) {
            $durasiKelas = $arrayFile[$jmlRuangan*4+$i*6+4];
            for ($k=0;$k<$durasiKelas;$k++)
                $this->arrayRuangan[0][$i][$k][1] = 1;
        }

//Dah, dengan ini $arrayFile sudah tidak dibutuhkan. Selesaaai!!!!

        /*

        $this->arrayRuangan
            -> array of Matkul + 1 (+1 nya ga akan dipakai)
                -> array of Time
                    -> 2 boolean [0] dan [1]
                       [0] gaboleh diubah (constraint)
                       [1] yang dipindah2in ()

        VARIABEL    => <Jam, Hari> di seluruh ruangan
        DOMAIN      => Matkul
        CONSTRAINT  =>
            (BELUM LENGKAP) Membatasi waktu bisa diisi sama ruangan apa

        function getIndex($hari,$waktu)
        array_search($ruangan,$indexRuangan)
        */
    }
}

