<?php namespace ewma\routers\schemas;

class Session extends \Schema
{
    public $table = 'ewma_routers';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('position')->default(0);
            $table->boolean('enabled')->default(false);
            $table->text('hosts');
            $table->string('name')->default('');
            $table->string('data_cat')->default(''); // todo not used
            $table->text('data'); // todo not used
        };
    }
}
