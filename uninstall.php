<?php

/**
 * Namespace base do Plugin
 * @doc: http://php.net/manual/pt_BR/language.namespaces.php
 */

namespace AgendaContatos;

use AgendaContatos\model\Contato;
use MocaBonita\MocaBonita;

/**
 * Impedir que o plugin seja carregado fora do Wordpress
 * @doc: https://codex.wordpress.org/pt-br:Escrevendo_um_Plugin#Arquivos_de_Plugin
 */
if (!defined('ABSPATH')) {
    die('Acesso negado!' . PHP_EOL);
}

/**
 * Carregar o autoload do composer
 * Adicionar o namespace atual para ser interpretado pelo autoload do composer
 */
$pluginPath = plugin_dir_path(__FILE__);
$loader = require $pluginPath . "vendor/autoload.php";
$loader->addPsr4(__NAMESPACE__ . '\\', $pluginPath);

/**
 * Callback que será chamado ao apagar o plugin (Opicional)
 * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_uninstall
 */
MocaBonita::uninstall(function (MocaBonita $mocabonita) {
    Contato::dropSchemaModel();
});