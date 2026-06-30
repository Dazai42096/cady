<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GeneratorQuickController extends Controller
{
    public function create()
    {
        return view('dashboard.generators.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'serial_number' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'capacity_kva' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $generator = new Generator();

        $this->setIfColumnExists($generator, 'serial_number', $data['serial_number']);
        $this->setIfColumnExists($generator, 'name', $data['serial_number']);
        $this->setIfColumnExists($generator, 'model', $data['model'] ?? null);
        $this->setIfColumnExists($generator, 'brand', $data['brand'] ?? null);
        $this->setIfColumnExists($generator, 'capacity_kva', $data['capacity_kva'] ?? null);
        $this->setIfColumnExists($generator, 'power_kva', $data['capacity_kva'] ?? null);
        $this->setIfColumnExists($generator, 'kva', $data['capacity_kva'] ?? null);
        $this->setIfColumnExists($generator, 'location', $data['location'] ?? null);
        $this->setIfColumnExists($generator, 'status', $data['status']);
        $this->setIfColumnExists($generator, 'notes', $data['notes'] ?? null);

        $generator->save();

        return redirect()
            ->route('dashboard.generators.index')
            ->with('success', 'Generator created successfully.');
    }

    private function setIfColumnExists(Generator $generator, string $column, mixed $value): void
    {
        if (Schema::hasColumn('generators', $column)) {
            $generator->{$column} = $value;
        }
    }
}