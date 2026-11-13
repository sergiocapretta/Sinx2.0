<?php
/**
 * Catalogo dei plugin ufficialmente predisposti per Sinx.
 * Per aggiungere un'applicazione futura, registrarla qui e creare
 * la relativa procedura di configurazione.
 */
return [
    'reunion' => [
        'id' => 'reunion',
        'name' => 'Reunion',
        'description' => 'Organizza riunioni, raccogli le disponibilità dei partecipanti e individua la data più adatta per incontrarsi.',
        'version' => '1.0',
        'menu_label' => 'Organizza riunioni',
        'menu_icon' => 'event_available',
        'logo' => './plugins/assets/reunion.svg',
        'setup_page' => './PluginSetup.php?id=reunion',
    ],
];
