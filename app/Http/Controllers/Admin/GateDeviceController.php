<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateDevice;
use Illuminate\Http\Request;

class GateDeviceController extends Controller
{
    public function index()
    {
        $devices = GateDevice::orderByDesc('created_at')->get();

        return view('admin.gate-devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.gate-devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $device = GateDevice::create([
            'name' => $request->name,
            'device_key' => GateDevice::generateKey(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.gate-devices.index')
            ->with('newDeviceKey', $device->device_key)
            ->with('newDeviceName', $device->name)
            ->with('success', 'Device created. Copy the key now — it will not be shown again.');
    }

    public function toggle($id)
    {
        $device = GateDevice::findOrFail($id);
        $device->update(['is_active' => !$device->is_active]);

        return redirect()->route('admin.gate-devices.index')
            ->with('success', $device->name . ' ' . ($device->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy($id)
    {
        GateDevice::findOrFail($id)->delete();

        return redirect()->route('admin.gate-devices.index')
            ->with('success', 'Device removed.');
    }
}