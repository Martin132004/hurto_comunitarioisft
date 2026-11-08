<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// El agua sale solo de la red o del turno de canal: los huertos con pozo pasan a agua de red
// y cada método de riego queda compatible con su fuente de agua
class QuitarRiegoPorPozo extends Migration
{
    public function up()
    {
        $this->db->table('huertos')->where('fuente_agua', 'pozo')->update(['fuente_agua' => 'red']);

        // Surco y manto necesitan el caudal del canal; con agua de red se pasa a manguera
        $this->db->table('huertos')->where('fuente_agua', 'red')->whereIn('metodo_riego', ['surco', 'manto'])->update(['metodo_riego' => 'manguera']);

        // La aspersión necesita presión de red; con agua de turno se pasa a manguera
        $this->db->table('huertos')->where('fuente_agua', 'turno')->where('metodo_riego', 'aspersion')->update(['metodo_riego' => 'manguera']);
    }

    public function down()
    {
        // No se puede saber qué huertos tenían pozo: los datos quedan como están
    }
}
