@extends('layouts.app')

@section('title', __('qc.templates.title'))
@section('page_title', __('qc.templates.page_title'))

@section('top_actions')
  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
    <i data-lucide="file-plus"></i> {{ __('qc.templates.open_form') }}
  </a>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.templates.heading') }}</h1>
      <p>{{ __('qc.templates.subtitle') }}</p>
    </div>
  </div>

  <div class="notice-box" style="margin-bottom: 1.5rem;">
    <i data-lucide="info" style="color: var(--primary-text); flex-shrink: 0;"></i>
    <div>
      <strong>{{ __('qc.templates.notice_title') }}</strong>
      <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.88rem;">
        {{ __('qc.templates.notice_body') }}
      </p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>{{ __('qc.templates.col_doc') }}</th>
            <th>{{ __('qc.templates.col_name') }}</th>
            <th>{{ __('qc.templates.col_division') }}</th>
            <th>{{ __('qc.templates.col_drawing') }}</th>
            <th>{{ __('qc.templates.col_specs') }}</th>
            <th>{{ __('qc.templates.col_status') }}</th>
            <th style="text-align: right;">{{ __('qc.templates.col_action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($templates as $tpl)
            <tr>
              <td>
                <span class="mono" style="font-weight: 700; color: var(--primary-text);">
                  {{ $tpl['form_doc'] }}
                </span>
              </td>
              <td>
                <strong style="color: var(--text-main); font-size: 0.95rem;">{{ $tpl['name'] }}</strong>
                <span style="font-size: 0.75rem; color: var(--text-subtle); display: block;">Unit: {{ $tpl['model_unit'] }}</span>
              </td>
              <td>
                <span style="font-weight: 500;">{{ $tpl['division'] }}</span>
                <span style="font-size: 0.75rem; color: var(--text-subtle); display: block;">{{ $tpl['category'] }}</span>
              </td>
              <td class="mono" style="font-size: 0.8rem;">
                {{ $tpl['part_no'] }}
              </td>
              <td class="mono" style="font-size: 0.78rem; color: var(--text-subtle); max-width: 240px;">
                {{ $tpl['key_specs'] }}
              </td>
              <td>
                @if($tpl['is_active'])
                  <span class="badge badge-green">
                    <i data-lucide="check-circle" style="width: 12px; height: 12px; display: inline;"></i> {{ __('qc.templates.active_badge') }}
                  </span>
                @else
                  <span class="badge badge-slate">
                    <i data-lucide="clock" style="width: 12px; height: 12px; display: inline;"></i> {{ __('qc.templates.pending_badge') }}
                  </span>
                @endif
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if($tpl['is_active'])
                  <a href="{{ route('inspection.create') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="edit"></i> {{ __('qc.templates.use_btn') }}
                  </a>
                @else
                  <button type="button" class="btn btn-secondary btn-sm" onclick="window.qcAlert({ title: @json(__('qc.templates.alert_title')), message: @json(__('qc.templates.alert_msg')), variant: 'info' })">
                    <i data-lucide="upload"></i> {{ __('qc.templates.upload_excel') }}
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
