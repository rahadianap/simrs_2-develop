<template>
    <div style="padding:0;margin:0;">
        <table class="uk-table">
            <thead>
                <tr>
                    <th style="background-color:#3399ff;font-size:14px; font-weight:400;color:white;padding:0.4em;margin:0;text-align:center;">Tanggal</th>
                    <th style="background-color:#3399ff;font-size:14px; font-weight:400;color:white;padding:0.4em;margin:0;text-align:center;">Tempat Kerja</th>
                    <th style="background-color:#3399ff;font-size:14px; font-weight:400;color:white;padding:0.4em;margin:0;text-align:center;">Pengirim</th>
                    <th style="background-color:#3399ff;font-size:14px; font-weight:400;color:white;padding:0.4em;margin:0;text-align:center;">Status</th>
                    <th style="background-color:#3399ff;font-size:14px; font-weight:400;color:white;padding:0.4em;margin:0;text-align:center;">...</th>                    
                </tr>
            </thead>
            <tbody>
                <tr style="background-color:whitesmoke;height:400px;">
                    <td colspan="5" style="text-align:center; font-size:12px;font-style:italic;">
                        Anda tidak memiliki undangan bergabung.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    name : 'invitation-list',
    data() {
        return {

        }
    },
    methods : {
        
    }
}
</script>