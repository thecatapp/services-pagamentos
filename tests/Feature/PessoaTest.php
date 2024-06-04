<?php

namespace Tests\Feature;

use App\Enum\EnumTipoPessoa;
use App\Helpers\HelperTipoPessoa;
use App\Http\Services\ServicesPessoa;
use App\Models\Contato;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PessoaTest extends TestCase
{

    use DatabaseTransactions;

    protected $payloadBasico = [
        "nm_pessoa" => "Lucas Alves Cardoso de Jesus",
        "cpf_cnpj" => "08811779908",
        "email" => "lucasac131ardosoj@gmail.com"
    ];

    protected ServicesPessoa | null $ServicesPessoa;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->ServicesPessoa = app(ServicesPessoa::class);

    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub

        $this->ServicesPessoa = null;

    }

    public function testIdentificarTipoPessoa()
    {
        $result = HelperTipoPessoa::identificarTipoPessoa($this->payloadBasico["cpf_cnpj"]);

        $this->assertInstanceOf(EnumTipoPessoa::class, $result);

        $this->assertEquals(EnumTipoPessoa::PessoaFisica->value, $result->value);
    }

    public function testTratarDadosPessoa()
    {
        $result = $this->ServicesPessoa->tratarDadosPessoa($this->payloadBasico);

        $this->assertIsArray($result);

        $this->arrayHasKey($result["tp_pessoa"]);

        $this->assertNotEmpty($result["tp_pessoa"]);
        $this->assertNotEmpty($result["cpf_cnpj"]);
        $this->assertNotEmpty($result["nm_pessoa"]);

    }

    public function testSalvarDadosPessoa(): void
    {
        $countPessoa = Pessoa::get()->count();

        $Pessoa = $this->ServicesPessoa->cadastrarPessoas($this->payloadBasico);

        $newCountPessoa = Pessoa::get()->count();

        $this->assertGreaterThan($countPessoa, $newCountPessoa);

        $this->assertInstanceOf(Pessoa::class, $Pessoa);

        $this->assertNotEmpty($Pessoa);
        $this->assertNotNull($Pessoa);
    }

    public function testCadastrarContato()
    {
        $Pessoa = $this->ServicesPessoa->cadastrarPessoas($this->payloadBasico);

        $Contato = $this->ServicesPessoa->cadastrarContato($Pessoa, $this->payloadBasico["email"]);

        $this->assertInstanceOf(Contato::class, $Contato);

        $this->assertNotEmpty($Contato);
        $this->assertNotNull($Contato);
    }

    public function testCadastrarUser()
    {
        $Pessoa = $this->ServicesPessoa->cadastrarPessoas($this->payloadBasico);

        $User = $this->ServicesPessoa->cadastrarUser($Pessoa, $this->payloadBasico["email"]);

        $this->assertInstanceOf(User::class, $User);

        $this->assertNotEmpty($User);
        $this->assertNotNull($User);
    }
}








