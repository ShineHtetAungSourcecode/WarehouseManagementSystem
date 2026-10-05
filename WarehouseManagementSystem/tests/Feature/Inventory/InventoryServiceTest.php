<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(InventoryService::class);
    $this->inventory = Inventory::factory()->create(['quantity' => 10]);
});

test('receiving stock increases quantity and logs a movement', function () {
    $user = User::factory()->create();

    $movement = $this->service->receive($this->inventory, 5, $user, reason: 'Supplier delivery');

    expect($this->inventory->quantity)->toBe(15)
        ->and($movement->type)->toBe(StockMovementType::Receive)
        ->and($movement->quantity_change)->toBe(5)
        ->and($movement->user_id)->toBe($user->id);
});

test('picking stock decreases quantity', function () {
    $this->service->pick($this->inventory, 4);

    expect($this->inventory->quantity)->toBe(6)
        ->and($this->inventory->movements()->first()->quantity_change)->toBe(-4);
});

test('picking more than is available is refused and changes nothing', function () {
    expect(fn () => $this->service->pick($this->inventory, 11))
        ->toThrow(InsufficientStockException::class);

    expect($this->inventory->fresh()->quantity)->toBe(10)
        ->and($this->inventory->movements()->count())->toBe(0);
});

test('quantities must be positive', function (int $quantity) {
    expect(fn () => $this->service->receive($this->inventory, $quantity))
        ->toThrow(InvalidArgumentException::class);
})->with([0, -3]);

test('adjustments can go up or down but not below zero', function () {
    $this->service->adjust($this->inventory, -3, 'Damaged in storage');
    expect($this->inventory->quantity)->toBe(7);

    expect(fn () => $this->service->adjust($this->inventory, -8, 'Miscount'))
        ->toThrow(InsufficientStockException::class);
});

test('transfers move stock between warehouses all-or-nothing', function () {
    $otherWarehouse = Warehouse::factory()->create([
        'company_id' => $this->inventory->warehouse->company_id,
    ]);
    $destination = Inventory::factory()->create([
        'item_id' => $this->inventory->item_id,
        'warehouse_id' => $otherWarehouse->id,
        'quantity' => 0,
    ]);

    $this->service->transfer($this->inventory, $destination, 6);

    expect($this->inventory->quantity)->toBe(4)
        ->and($destination->fresh()->quantity)->toBe(6);

    // Too much: neither side should change.
    expect(fn () => $this->service->transfer($this->inventory, $destination, 5))
        ->toThrow(InsufficientStockException::class);

    expect($this->inventory->fresh()->quantity)->toBe(4)
        ->and($destination->fresh()->quantity)->toBe(6);
});

test('transfers between different items are refused', function () {
    $otherItemStock = Inventory::factory()->create();

    expect(fn () => $this->service->transfer($this->inventory, $otherItemStock, 1))
        ->toThrow(InvalidArgumentException::class);
});
