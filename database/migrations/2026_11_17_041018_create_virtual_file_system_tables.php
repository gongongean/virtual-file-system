
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. VIRTUAL DISKS
        Schema::create('virtual_disks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('disk_name');
            $table->unsignedInteger('total_blocks');
            $table->unsignedInteger('block_size_bytes')->default(1024);

            $table->timestamps();
        });

        // 2. DIRECTORIES
        Schema::create('directories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('virtual_disk_id')
                ->constrained('virtual_disks')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('directories')
                ->cascadeOnDelete();

            $table->string('directory_name');

            $table->timestamps();

            $table->index(['virtual_disk_id', 'parent_id']);
        });

        // 3. FILES
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('virtual_disk_id')
                ->constrained('virtual_disks')
                ->cascadeOnDelete();

            $table->foreignId('directory_id')
                ->constrained('directories')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('file_name');
            $table->string('extension', 20)->nullable();

            $table->unsignedBigInteger('file_size_bytes')->default(0);

           $table->enum('allocation_method', [
              'contiguous',
                 'linked'
            ])->default('contiguous');

            $table->timestamps();

            $table->index(['virtual_disk_id', 'directory_id']);
        });

        // 4. DISK BLOCKS
        Schema::create('disk_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('virtual_disk_id')
                ->constrained('virtual_disks')
                ->cascadeOnDelete();

            $table->unsignedInteger('block_number');

            // Simulated file contents
            $table->longText('block_data')->nullable();

            // Used by linked allocation
            $table->unsignedBigInteger('next_block_id')->nullable();

            $table->timestamps();

            $table->unique([
                'virtual_disk_id',
                'block_number'
            ]);
        });

        // 5. FILE BLOCKS
        Schema::create('file_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id')
                ->constrained('files')
                ->cascadeOnDelete();

            $table->foreignId('disk_block_id')
                ->constrained('disk_blocks')
                ->cascadeOnDelete();

            $table->unsignedInteger('block_order');

            $table->boolean('is_index_block')->default(false);

            $table->timestamps();

            $table->unique('disk_block_id');

            $table->unique([
                'file_id',
                'block_order'
            ]);
        });

        // 6. BITMAP ENTRIES
        Schema::create('bitmap_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('disk_block_id')
                ->constrained('disk_blocks')
                ->cascadeOnDelete();

            // 0 = Free, 1 = Occupied
            $table->boolean('bit_value')->default(false);

            $table->timestamps();

            $table->unique('disk_block_id');
        });

        // 7. FILE PERMISSIONS
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id')
                ->constrained('files')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->boolean('can_read')->default(false);
            $table->boolean('can_write')->default(false);
            $table->boolean('can_execute')->default(false);

            $table->timestamps();

            $table->unique(['file_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('bitmap_entries');
        Schema::dropIfExists('file_blocks');
        Schema::dropIfExists('disk_blocks');
        Schema::dropIfExists('files');
        Schema::dropIfExists('directories');
        Schema::dropIfExists('virtual_disks');
    }
};
