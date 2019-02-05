<?php namespace ewma\routers\schemas;

class Route extends \Schema
{
    public $table = 'ewma_routers_routes';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('router_id')->default(0);
            $table->integer('wrapper_id')->nullable(); // todo del|change to component_id
            $table->boolean('wrapper_enabled')->default(false); // todo del|change to component_enabled
            $table->integer('position')->default(0);
            $table->boolean('enabled')->default(true);
            $table->boolean('listen')->default(false);
            $table->string('pattern')->default('');
            $table->string('name')->default('');
            $table->string('type')->default(''); // todo ?
            $table->string('data_source')->default(''); // todo ?
            $table->string('data_cat')->default(''); // todo not used
            $table->enum('response_wrapper', ['NONE', 'HTML'])->default('NONE');
        };
    }
}
