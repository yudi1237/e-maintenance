<?php

namespace Tests;

use App\Models\Technician;
use App\Support\PicSession;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function withPicSession(array $attributes = []): Technician
    {
        $pic = Technician::factory()->create(array_merge([
            'name' => 'Andi PIC',
            'pin' => '1234',
            'is_active' => true,
        ], $attributes));

        PicSession::set($pic);

        return $pic;
    }

    protected function actingAsWithPic($user, array $picAttributes = []): static
    {
        $this->actingAs($user);
        $this->withPicSession($picAttributes);

        return $this;
    }
}
