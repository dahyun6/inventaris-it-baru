@extends('layout')

@section('title', __('Audit Activity Log'))

@section('header_actions')
<a href="{{ route('activity_logs.index') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-rotate me-1"></i> {{ __('Refresh Log') }}
</a>
@endsection

@section('content')
<style>
    .table-phoenix {
        margin-bottom: 0;
        font-size: 0.825rem;
        width: 100% !important;
        vertical-align: middle;
    }
    .table-phoenix thead th {
        background-color: #f8fafc !important;
        border-bottom: 1px solid var(--phoenix-border-color) !important;
        color: #525b75 !important;
        font-weight: 700 !important;
        font-size: 0.725rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 0.75rem 1rem !important;
        white-space: nowrap;
    }
    .table-phoenix tbody td {
        border-bottom: 1px solid var(--phoenix-border-color);
        color: var(--phoenix-text-body);
        padding: 0.75rem 1rem !important;
    }
    .table-phoenix tbody tr:hover {
        background-color: #f8fafc;
    }
    .user-avatar-log {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: var(--phoenix-primary-subtle);
        color: var(--phoenix-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
    }
</style>

<div class="phoenix-card">
    <div class="phoenix-card-header flex-wrap gap-2">
        <div>
            <h6 class="phoenix-card-title mb-0">
                <i class="fas fa-clock-rotate-left text-primary"></i>
                {{ __('Audit Log Aktivitas & Perubahan Data') }}
            </h6>
            <small class="text-muted">{{ __('Merekam histori pembuatan, modifikasi, dan penghapusan data secara transparan.') }}</small>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('activity_logs.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <select name="module" class="form-select form-select-sm" style="width: auto; font-size: 0.775rem;" onchange="this.form.submit()">
                <option value="all">{{ __('Semua Modul') }}</option>
                @foreach($availableModules as $modKey => $modLabel)
                    <option value="{{ $modKey }}" {{ request('module') === $modKey ? 'selected' : '' }}>{{ $modLabel }}</option>
                @endforeach
            </select>

            <select name="action" class="form-select form-select-sm" style="width: auto; font-size: 0.775rem;" onchange="this.form.submit()">
                <option value="all">{{ __('Semua Aksi') }}</option>
                <option value="CREATE" {{ request('action') === 'CREATE' ? 'selected' : '' }}>CREATE</option>
                <option value="UPDATE" {{ request('action') === 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                <option value="DELETE" {{ request('action') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
                <option value="HANDOVER" {{ request('action') === 'HANDOVER' ? 'selected' : '' }}>HANDOVER</option>
                <option value="COMPLETE" {{ request('action') === 'COMPLETE' ? 'selected' : '' }}>COMPLETE</option>
            </select>

            <div class="input-group input-group-sm" style="width: 220px;">
                <input type="text" name="q" class="form-control" placeholder="{{ __('Cari deskripsi / nama...') }}" value="{{ request('q') }}">
                <button type="submit" class="btn btn-phoenix-secondary"><i class="fas fa-search"></i></button>
            </div>
            @if(request()->hasAny(['module', 'action', 'q']))
                <a href="{{ route('activity_logs.index') }}" class="btn btn-phoenix-secondary btn-sm py-1 px-2" title="{{ __('Reset Filter') }}">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive">
            <table class="table table-phoenix w-100 mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">{{ __('NO') }}</th>
                        <th width="14%">{{ __('WAKTU & TANGGAL') }}</th>
                        <th width="14%">{{ __('PENGGUNA / AKUN') }}</th>
                        <th width="10%">{{ __('MODUL') }}</th>
                        <th width="9%">{{ __('AKSI') }}</th>
                        <th width="14%">{{ __('OBJEK TARGET') }}</th>
                        <th>{{ __('DESKRIPSI AKTIVITAS') }}</th>
                        <th width="10%">{{ __('IP ADDRESS') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                    <tr>
                        <td class="text-center text-muted">{{ $logs->firstItem() + $index }}</td>
                        <td>
                            <div class="font-monospace fw-bold text-dark lh-sm" style="font-size: 0.775rem;">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </div>
                            <small class="text-muted font-monospace" style="font-size: 0.7rem;">
                                {{ $log->created_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-log">
                                    {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                </div>
                                <div class="lh-sm">
                                    <span class="fw-bold text-dark d-block" style="font-size: 0.8rem;">{{ $log->user_name }}</span>
                                    @if($log->user_id)
                                        <small class="text-muted" style="font-size: 0.675rem;">ID: #{{ $log->user_id }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                @if($log->module === 'Aset')
                                    <i class="fas fa-laptop text-primary me-1"></i>
                                @elseif($log->module === 'Maintenance')
                                    <i class="fas fa-screwdriver-wrench text-warning me-1"></i>
                                @elseif($log->module === 'Helpdesk')
                                    <i class="fas fa-headset text-info me-1"></i>
                                @elseif($log->module === 'Handover')
                                    <i class="fas fa-file-invoice text-success me-1"></i>
                                @elseif($log->module === 'User')
                                    <i class="fas fa-user-gear text-secondary me-1"></i>
                                @else
                                    <i class="fas fa-folder text-primary me-1"></i>
                                @endif
                                {{ $log->module }}
                            </span>
                        </td>
                        <td>
                            @if($log->action === 'CREATE')
                                <span class="badge-phoenix badge-phoenix-success" style="font-size: 0.7rem;">CREATE</span>
                            @elseif($log->action === 'UPDATE')
                                <span class="badge-phoenix badge-phoenix-primary" style="font-size: 0.7rem;">UPDATE</span>
                            @elseif($log->action === 'DELETE')
                                <span class="badge-phoenix badge-phoenix-danger" style="font-size: 0.7rem;">DELETE</span>
                            @elseif($log->action === 'HANDOVER' || $log->action === 'COMPLETE')
                                <span class="badge-phoenix badge-phoenix-info" style="font-size: 0.7rem;">{{ $log->action }}</span>
                            @else
                                <span class="badge-phoenix badge-phoenix-secondary" style="font-size: 0.7rem;">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->subject_name)
                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.8rem;">{{ $log->subject_name }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark" style="font-size: 0.8125rem;">{{ $log->description ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="font-monospace text-muted small">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-clock-rotate-left fs-2 opacity-25 mb-2"></i><br>
                            {{ __('Belum ada catatan aktivitas log yang sesuai.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                {{ __('Menampilkan') }} {{ $logs->firstItem() }} - {{ $logs->lastItem() }} {{ __('dari') }} {{ $logs->total() }} {{ __('aktivitas') }}
            </span>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
        @else
        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center text-muted small font-monospace">
            <span>Audit Trail & Integrity Log System</span>
            <span>Total: {{ $logs->total() }} Records</span>
        </div>
        @endif
    </div>
</div>
@endsection
