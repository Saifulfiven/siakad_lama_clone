@if ( in_array(61101, Sia::getProdiUser()) )
    <table width="100%">
        <tr>
            <td style="text-align: center">Direktur<br>Program Pascasarjana</td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}<br>
                Ketua
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br><br><br></td></tr>
        <tr>
            <td style="text-align: center"><b>{{ Sia::option('direktur_pps') }}</b></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ Sia::option('ketua') }}</b></td>
        </tr>
    </table>

@else
    <table width="100%">
        <tr>
            <td style="text-align: center">Wakil Ketua<br>Bidang Akademik</td>
            <td width="40%">&nbsp;</td>
            <td style="text-align: center">
                Makassar, {{ Rmt::tgl_indo(Carbon::now()->format('Y-m-d')) }}<br>
                Ketua
            </td>
        </tr>
        <tr><td colspan="3"><br><br><br><br><br><br></td></tr>
        <tr>
            <td style="text-align: center"><b>{{ Sia::option('ketua_1') }}</b></td>
            <td>&nbsp;</td>
            <td style="text-align: center"><b>{{ Sia::option('ketua') }}</b></td>
        </tr>
    </table>
@endif