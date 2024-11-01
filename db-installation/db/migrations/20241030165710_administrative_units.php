<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdministrativeUnits extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        
        // create the table permission
        $tableAdminUnit = $this->table('administrative_unit');
        $tableAdminUnit->addColumn('code', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('parent_code', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('parent_code_all', 'text', ['null' => true])
            ->addIndex(['code'], ['unique' => true, 'name' => 'idx_administrative_unit_code'])
            ->addForeignKey('parent_code', 'administrative_unit', 'code', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();
        
        $tableInstitution = $this->table('institution');
        $tableInstitution->addColumn('code', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 200, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('address_1', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('address_2', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('postal_code', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('city', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('parent_code', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('parent_code_all', 'text', ['null' => true])
            ->addIndex(['code'], ['unique' => true, 'name' => 'idx_institution_code'])
            ->addForeignKey('parent_code', 'institution', 'code', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();

        $tableMedicines = $this->table('medicine');
        $tableMedicines->addColumn('code', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('dosage', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('presentation', 'string', ['limit' => 50, 'null' => false])
            ->addIndex(['code'], ['unique' => true, 'name' => 'idx_medicine_code'])
            ->create();
    }
}
