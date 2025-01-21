<?php
return [
    'action_success' => 'Ação realizada com sucesso',
    'action_generic_error' => 'Ocorreu um erro: %s',
    'action_file_error' => 'O arquivo enviado é inválido.',
    'action_delete_confirm' => 'Confirma e exclusão do registro?',
    'user_invalid_login' => 'Email ou senha inválidos',
    'user_no_permission' => 'Sem permissão para executar essa ação.',
    'field_required' => 'O campo %s é de preenchimento obrigatório.',
    'field_duplicated' => 'Duplicado! Já existe um registro com %s: %s',
    'yes' => 'Sim',
    'enum' => [
        'order_medicine_status' => [
            'OPEN' => 'Criado',
            'APPROVED' => 'Aprovado',
            'DONE' => 'Finalizado',
            'CANCELED' => 'Cancelado'
        ],
    ],
    'general_status' => [
        'OPEN' => 'Criado',
        'DONE' => 'Finalizado'
    ],
    'medicines_msg' => [
        'inventory_no_supplier' => 'Instituição não possui Fornecedor para realizar essa ação.',
        'inventory_no_order' => 'Pedido náo localizado ou a situaçao foi alterada.',
    ],
    'medicines_category' => [
        'ORAL' => 'Oral',
        'INJECTABLE' => 'Injetável',
    ],
    'medicines_dosage_unit' => [
        'MG' => 'MG',
        'ML' => 'ML',
    ],
    'medicines_presentation' => [
        'BOTTLE' => 'Frasco',
        'TABLET' => 'Compromido',
        'AMPOULE' => 'Ampoula',
        'CAPSULE' => 'Cápsula'
    ],
    'institution_types' => [
        'NOTIFICATION' => 'Notificação',
        'TREATMENT' => 'Tratamento',
        'MEDICINE_STORE' => 'Armazenamento de Medicamento'
    ],
    'inventory_history' => [
        'BATCH' => 'Inserão de Lote',
        'ORDER_APPROVED' => 'Pedido Aprovado',
        'ORDER_RECEIVED' => 'Pedido Recebido',
        'TRANSFER_CREATED' => 'Transferência Criada',
        'TRANSFER_RECEIVED' => 'Transferência Recebido'
    ],
    'user_view_type' => [
        'ALL' => 'Sem Restrição',
        'UNIT' => 'Unidade administrativa',
        'INSTITUTION' => 'Instituição'
    ],
    'roles' => [
        'DEV' => 'Desenvolvedor',
        'USER_VIEW' => 'Visualizar usuários',
        'USER_ADD' => 'Adicionar usuário',
        'USER_UPD' => 'Atualizar usuário',
        'USER_DEL' => 'Remover usuário',
        'PROFILE_VIEW' => 'Visualizar perfils de acesso ',
        'PROFILE_ADD' => 'Adicionar perfil de acesso',
        'PROFILE_UPD' => 'Atualizar perfil de acesso',
        'PROFILE_DEL' => 'Remover perfil de acesso',
        'MEDICINE_VIEW' => 'Visualizar medicamentos',
        'MEDICINE_ADD' => 'Adicionar medicamento',
        'MEDICINE_UPD' => 'Atualizar medicamento',
        'MEDICINE_DEL' => 'Remover medicamento',
        'INST_VIEW' => 'Visualizar instituições',
        'INST_ADD' => 'Adicionar instituição',
        'INST_UPD' => 'Atualizar instituição',
        'INST_DEL' => 'Remover instituição',
        'NOTICE_VIEW' => 'Visualizar avisos',
        'NOTICE_ADD' => 'Adicionar aviso',
        'NOTICE_UPD' => 'Atualizar aviso',
        'NOTICE_DEL' => 'Remover aviso',
        'UNIT_VIEW' => 'Visualizar unidades administrativas',
        'UNIT_ADD' => 'Adicionar unidade administrativa',
        'UNIT_UPD' => 'Atualizar unidade administrativa',
        'UNIT_DEL' => 'Remover unidade administrativa',
        'UNIT_IMPORT' => 'Importar unidades administrativas',
        'UNIT_SWITCH' => 'Mudar de instituição',
        'INVENTORY_BATCH_CREATE' => 'Criar Batch',
        'INVENTORY_ORDER_VIEW' => 'Visualizar Pedidos',
        'INVENTORY_ORDER_CREATE' => 'Criar Pedido',
        'INVENTORY_ORDER_CANCEL' => 'Cancelar Pedido',
        'INVENTORY_ORDER_APPROVE' => 'Aprovar Pedido',
        'INVENTORY_ORDER_RECEIVE' => 'Notificar Recebimento de Pedido',
        'INVENTORY_TRANSFER_VIEW' => 'Visualizar Transferências',
        'INVENTORY_TRANSFER_CREATE' => 'Criar Transferência',
        'INVENTORY_TRANSFER_CANCEL' => 'Cancelar Transferência',
        'INVENTORY_TRANSFER_RECEIVE' => 'Notificar Recebimento de Transferência',
        'INVENTORY_DISPENSATION_VIEW' => 'Visualizar Dispensações',
        'INVENTORY_DISPENSATION_CREATE' => 'Realizar Dispensação'
    ]
];