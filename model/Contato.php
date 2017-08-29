<?php

namespace AgendaContatos\model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use MocaBonita\model\MbWpUser;
use MocaBonita\tools\eloquent\MbModel;
use MocaBonita\tools\validation\MbNumberValidation;
use MocaBonita\tools\validation\MbStringValidation;
use MocaBonita\tools\validation\MbValidation;

/**
 * Class Contato Model
 *
 * @package AgendaContatos\model
 */
class Contato extends MbModel
{
    /**
     * Habilitar softdelete para esta model
     */
    use SoftDeletes;

    /**
     * Atributos do banco que podem ser inseridos em massa
     */
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'wp_user_id',
    ];

    /**
     * Atributos do banco que são datas
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Tabela de contatos
     *
     */
    public function createSchema(Blueprint $table)
    {
        $table->increments($this->getKeyName());
        $table->string('nome');
        $table->string('email');
        $table->string('telefone');
        $table->unsignedBigInteger('wp_user_id');
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('wp_user_id')
            ->references('ID')
            ->on((new MbWpUser)->getTable())
            ->onDelete('cascade');
    }

    /**
     * Validação dos dados
     */
    protected function validation(array $attributes)
    {
        $validation = MbValidation::validate($attributes);

        /**
         * Atribuir automaticamente o id do usuário atual do wordpress ao criar.
         */
        if(!$this->exists){
            Arr::set($attributes, 'wp_user_id', MbWpUser::getCurrentUser()->getKey());
        }

        $validation->setRemoveUnused(true);
        $validation->setValidations("nome", MbStringValidation::getInstance(), [
            'min' => 5,
            'max' => 100,
            'trim' => true,
            'striptags' => true,
            'str_upper' => true,
        ]);
        $validation->setValidations("email", MbStringValidation::getInstance(), [
            'min' => 5,
            'max' => 100,
            'email' => true,
        ]);
        $validation->setValidations("telefone", MbStringValidation::getInstance(), [
            'min' => 11,
            'max' => 12,
            'regex' => "/^[0-9]{2}-([0-9]{8}|[0-9]{9})/",
        ]);
        $validation->setValidations("wp_user_id", MbNumberValidation::getInstance(), [
            'min' => 1,
        ]);

        return $validation;
    }

    /**
     * Buscar contato do usuário atual por id
     *
     * @param $contatoId
     * @return Contato
     * @throws \Exception
     */
    public static function buscarPorId($contatoId)
    {

        $model = new Contato;

        $contato = Contato::where($model->getKeyName(), $contatoId)
            ->where('wp_user_id', MbWpUser::getCurrentUser()->getKey())
            ->first();

        if ($contato instanceof Contato) {
            return $contato;
        } else {
            throw new \Exception("Nenhum contato foi encontrado com este ID.");
        }

    }

    /**
     * Cadastrar um novo contato associado ao usuário atual
     *
     * @return Contato
     */
    public function cadastrar()
    {
        $this->setAttribute('wp_user_id', MbWpUser::getCurrentUser()->getKey());
        $this->save();

        return $this;
    }


    /**
     * Atualizar um novo contato associado ao usuário atual
     *
     * @return Contato
     */
    public function atualizar(array $dados)
    {
        /**
         * Evitar que o wp_user_id seja atualizado
         *
         */
        Arr::set($dados, 'wp_user_id', $this->getAttribute('wp_user_id'));
        $this->update($dados);

        return $this;
    }


    /**
     * Apagar contato associado ao usuário atual
     *
     * @param int $contatoId
     * @return bool
     */
    public function apagar($contatoId)
    {
        $contato = Contato::buscarPorId($contatoId);
        return $contato->delete();
    }
}