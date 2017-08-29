<?php

namespace AgendaContatos\controller;

use AgendaContatos\model\Contato;
use Illuminate\Database\Eloquent\Collection;
use MocaBonita\controller\MbController;
use MocaBonita\tools\MbException;
use MocaBonita\tools\MbRequest;
use MocaBonita\tools\MbResponse;

/**
 * Class AgendaContatosController
 *
 * @package AgendaContatos\controller
 * @author Jhordan Lima
 */
class AgendaContatosController extends MbController
{

    /**
     * Action index
     *
     * @param MbRequest $mbRequest
     * @param MbResponse $mbResponse
     * @return mixed|\MocaBonita\view\MbView|null|string
     *
     */
    public function indexAction(MbRequest $mbRequest, MbResponse $mbResponse)
    {
        $this->getMbView()->with("contatos", Contato::all());
        return $this->getMbView();
    }

    /**
     * Action cadastrar
     *
     * Caso o método de requisição seja post, salvará os dados
     * Caso o método não seja post, mostrará a view
     *
     * @param MbRequest $mbRequest
     * @param MbResponse $mbResponse
     * @return \MocaBonita\view\MbView
     * @throws MbException
     */
    public function cadastrarAction(MbRequest $mbRequest, MbResponse $mbResponse)
    {
        $dadosPost = $mbRequest->inputSource();

        $contato = new Contato($dadosPost);

        $this->getMbView()->setAction('formulario');
        $this->getMbView()->with('contato', $contato);

        try {

            if ($mbRequest->isMethod('POST')) {
                $contato->cadastrar();
                $mbResponse->redirect($mbRequest->fullUrlWithNewAction('index'));
            }

            return $this->getMbView();

        } catch (MbException $e) {
            $e->setExceptionData($this->getMbView());
            throw $e;
        } catch (\Exception $e) {
            throw new MbException($e->getMessage(), 400, $this->getMbView());
        }
    }

    /**
     * Action atualizar
     *
     * Caso o método de requisição seja post, salvará os dados
     * Caso o método não seja post, mostrará a view
     *
     * @param MbRequest $mbRequest
     * @param MbResponse $mbResponse
     * @return \MocaBonita\view\MbView
     * @throws MbException
     * @throws \Exception
     */
    public function atualizarAction(MbRequest $mbRequest, MbResponse $mbResponse)
    {
        $contatoId = $mbRequest->query('id');
        $dadosPost = $mbRequest->inputSource();

        try {
            $contato = Contato::buscarPorId($contatoId);
        } catch (\Exception $e) {
            throw new \Exception("Nenhum contato com este ID foi encontrado!");
        }

        $this->getMbView()->setAction('formulario');
        $this->getMbView()->with('contato', $contato);

        try {

            if ($mbRequest->isMethod('POST')) {
                $contato->atualizar($dadosPost);
                $mbResponse->redirect($mbRequest->fullUrlWithNewAction('index'));
            }

            return $this->getMbView();

         } catch (MbException $e) {
            $contato->fill($dadosPost);
            $e->setExceptionData($this->getMbView());
            throw $e;
        } catch (\Exception $e) {
            $contato->fill($dadosPost);
            throw new MbException($e->getMessage(), 400, $this->getMbView());
        }
    }

    /**
     * Action apagar
     *
     * @param MbRequest $mbRequest
     */
    public function apagarAction(MbRequest $mbRequest)
    {
        $contato = new Contato();
        $contato->apagar($mbRequest->query('id'));
        $this->getMbResponse()->redirect($mbRequest->fullUrlWithNewAction('index'));
    }

    /**
     * Shortcode agenda de contatos [contatos]
     *
     * @param array $attributes
     * @param string $content
     * @param array $tags
     *
     * @return \MocaBonita\view\MbView
     */
    public function agendaContatosShortcode($attributes, $content, $tags)
    {
        $this->getMbView()
            ->setPage('agenda-contatos')
            ->setAction('index');

        $this->getMbView()->with("contatos", Contato::all());

        return $this->getMbView();
    }

    /**
     * Action buscar todos
     *
     * @return Collection
     *
     */
    public function buscarTodosAction()
    {
        return Contato::all();
    }
}