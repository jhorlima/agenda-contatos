<?php
/**
 * Plugin Name: Agenda de Contatos
 * Plugin URI: http://exemplo.plugin.com
 * Description: Plugin para gerenciar uma agenda de contatos
 * Version: 1.0.0
 * Author: Jhordan Lima
 * Author URI: http://www.github.com/jhorlima
 * License: GLP
 *
 * @doc: https://developer.wordpress.org/plugins/the-basics/header-requirements/
 */

/**
 * Namespace base do Plugin
 * @doc: http://php.net/manual/pt_BR/language.namespaces.php
 */

namespace AgendaContatos;

use AgendaContatos\controller\AgendaContatosController;
use AgendaContatos\model\Contato;
use MocaBonita\MocaBonita;
use MocaBonita\tools\MbPage;
use MocaBonita\tools\MbPath;

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
 * Callback que será chamado ao ativar o plugin (Opicional)
 * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_active
 */
MocaBonita::active(function () {
    Contato::createSchemaModel();
});

/**
 * Callback que será chamado ao desativar o plugin (Opicional)
 * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_deactive
 */
MocaBonita::deactive(function () {
    Contato::truncate();
});

/**
 * Callback que terão as configurações do plugin
 * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_plugin
 */
MocaBonita::plugin(function (MocaBonita $mocabonita) {

    /**
     * Criando uma página para o Plugin
     */
    $agendaContatosPagina = MbPage::create('Agenda Contatos');

    /**
     * Aqui podemos configurar alguns ajustes da página
     *
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.tools.MbPage.html
     * @doc: https://developer.wordpress.org/resource/dashicons/#book
     */
    $agendaContatosPagina->setDashicon('dashicons-book')
        ->setController(AgendaContatosController::class);

    $agendaContatosPagina->addMbAction('cadastrar');

    $agendaContatosPagina->addMbAction('atualizar');

    $agendaContatosPagina->addMbAction('apagar');

    /**
     * É possível também definir assets ao plugin, wordpress ou página, basta obter seu MbAsset.
     * Nos assets é possível adicionar css e javascript ao Wordpress.
     * A class MbPath também pode ser utilizada para auxiliar nos diretórios do wordpress.
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.tools.MbAsset.html
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.tools.MbPath.html
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_getAssetsPlugin
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_getAssetsWordpress
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.tools.MbPage.html#method_getMbAsset
     */
    $agendaContatosPagina->getMbAsset()
        ->setCss(MbPath::pBwDir("bootstrap/dist/css/bootstrap.min.css"))
        ->setCss(MbPath::pCssDir("bootstrap-fix-wp.css"))
        ->setJs(MbPath::pBwDir("jquery/dist/jquery.min.js"))
        ->setJs(MbPath::pBwDir("bootstrap/dist/js/bootstrap.min.js"));

    /**
     * Após finalizar todas as configurações da página, podemos adiciona-las ao MocaBonita para que elas possam ser
     * usadas pelo Wordpress. Caso uma página não seja adicionada, apenas os shortcodes relacionados a ela serão
     * executados.
     */
    $mocabonita->addMbPage($agendaContatosPagina);

    /**
     * Caso seu plugin precise de um shortcode, você pdoe adiciona-lo associando à página.
     * Seu comportamento é semelhante a de uma action, contudo seu sufixo deve ser "Shortcode", Ex: exemploShortcode(array $attributes, $content, $tags).
     * @doc: https://codex.wordpress.org/Shortcode_API
     * @doc: https://jhorlima.github.io/wp-mocabonita/classes/MocaBonita.MocaBonita.html#method_addMbShortcode
     *
     * @shortcode:[contatos]
     */
    $mocabonita->addMbShortcode('contatos', $agendaContatosPagina, 'agendaContatos');

}, true);
//O ultimo parâmetro de MocaBonita::plugin é opcional e define se o plugin está em desenvolvimento.