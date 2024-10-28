<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialMigration extends AbstractMigration
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
        $tablePer = $this->table('permission');
        $tablePer->addColumn('name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('is_internal', 'boolean', ['signed' => false])
            ->create();

        // create the permisson roles
        $tablePerRoles = $this->table('permission_roles');
        $tablePerRoles->addColumn('permission_id', 'integer', ['null' => false])
            ->addColumn('role', 'string', ['limit' => 100, 'null' => false])
            ->addIndex(['permission_id', 'role'], [
                  'unique' => true,
                  'name' => 'uq_permission_role',
            ])
            ->addForeignKey('permission_id', 'permission', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();

        // create the table users
        $tableUsers = $this->table('user_account');
        $tableUsers->addColumn('name', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 150, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 150, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('permission_id', 'integer', ['null' => false])
            ->addForeignKey('permission_id', 'permission', 'id', ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'])
            ->create();

        // create the table to log actions
        $tableLogAction = $this->table('log_action');
        $tableLogAction->addColumn('user_account_id', 'integer', ['null' => true])
            ->addColumn('owner_type', 'string', ['limit' => 150, 'null' => false])
            ->addColumn('owner_id', 'integer', ['null' => false])
            ->addColumn('action_type', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('old_value', 'text')
            ->addColumn('new_value', 'text')
            ->addColumn('created_at', 'datetime')
            ->create();

        /**
         * Migrate Up.
         */
        if ($this->isMigratingUp()) {
            $tablePer = $this->table('permission');
            $tablePer->insert(['name'  => 'Developer', 'is_internal' => true])->saveData();

            $rowPer = $this->fetchRow('SELECT id FROM permission');
    
            $tablePerRoles = $this->table('permission_roles');
            $tablePerRoles->insert(['permission_id' => $rowPer['id'], 'role'  => 'DEV'])->saveData();
    
            $tableUser = $this->table('user_account');
            $tableUser->insert([
                'name'  => 'André Rodrigues',
                'email'  => 'andreluizweb@gmail.com',
                'password'  => '$2y$10$V7J3r.1hOJ45HTxr4nhqvuqyFbOZzgskY6QTaRw7X2gBF/bVjTx8.', // teste123
                'permission_id'  => $rowPer['id']
            ])->saveData();
        }

    }

}
