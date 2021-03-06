<?php

use Watson\BootstrapForm\BootstrapForm;

class BootstrapFormTest extends PHPUnit_Framework_TestCase
{
    protected $bootstrapForm;

    protected $htmlBuilderMock;

    protected $formBuidlerMock;

    protected $configMock;

    protected $sessionMock;

    public function setUp()
    {
        $this->htmlBuilderMock = Mockery::mock('Collective\Html\HtmlBuilder');
        $this->formBuidlerMock = Mockery::mock('Collective\Html\FormBuilder');
        $this->configMock = Mockery::mock('Illuminate\Config\Repository')->shouldDeferMissing();
        $this->sessionMock = Mockery::mock('Illuminate\Session\SessionManager')->shouldDeferMissing();

        $this->bootstrapForm = new BootstrapForm(
            $this->htmlBuilderMock,
            $this->formBuidlerMock,
            $this->configMock,
            $this->sessionMock
        );
    }

    /** @test */
    public function it_opens_default_form()
    {
        $this->formBuidlerMock->shouldReceive('open')->once()->with([
            'role' => 'form'
        ])->andReturn('foo');

        $this->configMock->shouldReceive('get')
            ->with('bootstrap-form::default_form')
            ->once()
            ->andReturn(null);

        $result = $this->bootstrapForm->open();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_store_model_form()
    {
        $model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $model->exists = false;

        $this->formBuidlerMock->shouldReceive('model')
            ->once()
            ->with($model, [
                'role' => 'form',
                'route' => 'bar',
                'method' => 'POST'
            ])
            ->andReturn('foo');

        $result = $this->bootstrapForm->open([
            'model' => $model,
            'store' => 'bar',
            'update' => 'baz'
        ]);

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_update_model_form()
    {
        $model = Mockery::mock('Illuminate\Database\Eloquent\Model');
        $model->exists = true;

        $model->shouldReceive('getKey')
            ->once()
            ->andReturn(1);

        $this->formBuidlerMock->shouldReceive('model')
            ->once()
            ->with($model, [
                'role' => 'form',
                'route' => ['baz', 1],
                'method' => 'PUT'
            ])
            ->andReturn('foo');

        $result = $this->bootstrapForm->open([
            'model' => $model,
            'store' => 'bar',
            'update' => 'baz'
        ]);

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_a_standard_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'class' => '',
                'role' => 'form'
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->openStandard();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_an_inline_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'class' => 'form-inline',
                'role' => 'form'
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->openInline();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_opens_a_horizontal_form()
    {
        $this->formBuidlerMock->shouldReceive('open')
            ->with([
                'class' => 'form-horizontal',
                'role' => 'form'
            ])
            ->once()
            ->andReturn('foo');

        $result = $this->bootstrapForm->openHorizontal();

        $this->assertEquals('foo', $result);
    }

    /** @test */
    public function it_closes_a_form()
    {
        $this->formBuidlerMock->shouldReceive('close')->once()->andReturn('foo');

        $result = $this->bootstrapForm->close();

        $this->assertEquals('foo', $result);
    }
}