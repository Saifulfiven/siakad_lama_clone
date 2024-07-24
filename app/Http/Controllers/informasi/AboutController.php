<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session, Rmt;
use App\InformasiModels\About, App\InformasiModels\Faspeta;
use DB;

class AboutController extends Controller
{
    private $f = 'informasi.about.';

/* Profil */
    public function profil(Request $r)
    {

        $data['profil'] = About::where('key', 'profil');

        return view($this->f."profil", $data);
    }

    public function updateProfil(Request $r)
    {
        $this->validate($r, [ 'konten' => 'required' ]);

        $data = About::where('key','profil')->update(['value' => $r->konten]);

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

/* Visi Misi */
    public function visi(Request $r)
    {

        $data['visi_sarjana'] = About::where('key', 'visi_sarjana');
        $data['visi_pascasarjana'] = About::where('key', 'visi_pascasarjana');

        return view($this->f."visi", $data);
    }

    public function updateVisi(Request $r)
    {
        $this->validate($r, [ 'konten' => 'required', 'konten2' => 'required' ]);

        About::where('key','visi_sarjana')->update(['value' => $r->konten]);
        About::where('key','visi_pascasarjana')->update(['value' => $r->konten2]);

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

/* Keunggulan */
    public function keunggulan(Request $r)
    {

        $data['keunggulan'] = About::where('key', 'keunggulan');

        return view($this->f."keunggulan", $data);
    }

    public function updatekeunggulan(Request $r)
    {
        $this->validate($r, [ 'konten' => 'required' ]);

        About::where('key','keunggulan')->update(['value' => $r->konten]);

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

/* Prodi */
    public function prodi(Request $r)
    {

        $data['prodi_sarjana'] = About::where('key', 'prodi_sarjana');
        $data['prodi_pascasarjana'] = About::where('key', 'prodi_pascasarjana');

        return view($this->f."prodi", $data);
    }

    public function updateProdi(Request $r)
    {
        $this->validate($r, [ 'konten' => 'required', 'konten2' => 'required' ]);

        About::where('key','prodi_sarjana')->update(['value' => $r->konten]);
        About::where('key','prodi_pascasarjana')->update(['value' => $r->konten2]);

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

/* Fasilitas */
    public function fasilitas(Request $r)
    {

        $data['fasilitas'] = Faspeta::where('jenis','fasilitas')->orderBy('urutan','asc')->get();

        return view($this->f."fasilitas", $data);
    }

    public function fasilitasUrutan(Request $r)
    {
        $urutan = explode("&",$r->urutan);
        $no = 1;

        foreach( $urutan as $val ){
            $val = explode("=", $val);

            DB::table('fasilitas_peta')->where('id', $val[1])->update(['urutan' => $no]);
            $no++;
        }
    }

    public function fasilitasCreate()
    {
        return view($this->f."fasilitas-create");
    }

    public function fasilitasStore(Request $r)
    {
        $this->validate($r, [
                'nama_fasilitas' => 'required|max:255',
                'keterangan' => 'required',
            ]);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->nama_fasilitas, $r->file('gambar'),'fasilitas');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            }
        } else {
            Rmt::Error('Gambar masih kosong');
            return redirect()->back()->withInput();
        }

        $data = new Faspeta;
        $data->judul        = $r->nama_fasilitas;
        $data->deskripsi    = $r->keterangan;
        $data->gambar       = $nm_gambar;
        $data->urutan       = $r->urutan;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('fasilitas_create', ['urutan' => $r->urutan + 1]));
    }

    public function fasilitasEdit(Request $r)
    {
        $data['r'] = Faspeta::find($r->id);

        return view($this->f.'fasilitas-edit', $data);
    }

    public function fasilitasUpdate(Request $r)
    {
        $this->validate($r, [
                'nama_fasilitas' => 'required|max:255',
                'keterangan' => 'required',
            ]);

        $data = Faspeta::find($r->id);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->nama_fasilitas, $r->file('gambar'),'fasilitas');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            }

            $data->gambar = $nm_gambar;

            if ( file_exists(storage_path().'/fasilitas/'.$r->gambar_lama) ) {
                unlink(storage_path().'/fasilitas/'.$r->gambar_lama);
            }
        }

        $data->judul        = $r->nama_fasilitas;
        $data->deskripsi    = $r->keterangan;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function fasilitasDelete($id)
    {
        $data = Faspeta::find($id);
        if ( !empty($data->gambar) && file_exists(storage_path().'/fasilitas/'.$data->gambar) ) {
            unlink(storage_path().'/fasilitas/'.$data->gambar);
        }
        Faspeta::where('id', $id)->delete();

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

/* Peta */
    public function peta(Request $r)
    {

        $data['peta'] = Faspeta::where('jenis', 'peta')->orderBy('urutan','asc')->get();

        return view($this->f."peta", $data);
    }

    public function petaUrutan(Request $r)
    {
        $urutan = explode("&",$r->urutan);
        $no = 1;

        foreach( $urutan as $val ){
            $val = explode("=", $val);

            DB::table('fasilitas_peta')->where('id', $val[1])->update(['urutan' => $no]);
            $no++;
        }
    }

    public function petaCreate()
    {
        return view($this->f."peta-create");
    }

    public function petaStore(Request $r)
    {
        $this->validate($r, [
                'nama_peta' => 'required|max:255',
                'keterangan' => 'required',
            ]);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->nama_peta, $r->file('gambar'),'peta');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            } 
        } else {
            Rmt::Error('Gambar masih kosong');
            return redirect()->back()->withInput();
        }

        $data = new Faspeta;
        $data->judul        = $r->nama_peta;
        $data->deskripsi    = $r->keterangan;
        $data->gambar       = $nm_gambar;
        $data->jenis        = 'peta';
        $data->urutan       = $r->urutan;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('peta_create', ['urutan' => $r->urutan + 1]));
    }

    public function petaEdit(Request $r)
    {
        $data['r'] = Faspeta::find($r->id);

        return view($this->f.'peta-edit', $data);
    }

    public function petaUpdate(Request $r)
    {
        $this->validate($r, [
                'nama_peta' => 'required|max:255',
                'keterangan' => 'required',
            ]);

        $data = Faspeta::find($r->id);

        if ( $r->hasFile('gambar') ) {
            $nm_gambar = Rmt::uploadGambar($r->nama_peta, $r->file('gambar'),'peta');
            if ( !$nm_gambar ) {
                Rmt::Error('Gagal mengupload gambar');
                return redirect()->back()->withInput();
            }
            
            $data->gambar = $nm_gambar;

            if ( file_exists(storage_path().'/peta/'.$r->gambar_lama) ) {
                unlink(storage_path().'/peta/'.$r->gambar_lama);
            }
        }

        $data->judul        = $r->nama_peta;
        $data->deskripsi    = $r->keterangan;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function petaDelete($id)
    {
        $data = Faspeta::find($id);
        if ( !empty($data->gambar) && file_exists(storage_path().'/peta/'.$data->gambar) ) {
            unlink(storage_path().'/peta/'.$data->gambar);
        }
        Faspeta::where('id', $id)->delete();

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
