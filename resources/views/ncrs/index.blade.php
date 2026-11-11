@extends('layouts.app')

@section('title', __('qc.ncrs.title'))
@section('page_title', __('qc.ncrs.page_title'))

@section('top_actions')
  <button class="btn btn-danger btn-sm" onclick="window.qcAlert({ title: @json(__('qc.ncrs.modal_title')), message: @json(__('qc.ncrs.modal_msg')), variant: 'info' })">
    <i data-lucide="alert-octagon"></i> {{ __('qc.ncrs.add_btn') }}
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.ncrs.heading') }}</h1>
      <p>{{ __('qc.ncrs.subtitle') }}</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>{{ __('qc.ncrs.col_no') }}</th>
            <th>{{ __('qc.ncrs.col_ref') }}</th>
            <th>{{ __('qc.ncrs.col_customer') }}</th>
            <th>{{ __('qc.ncrs.col_issue') }}</th>
            <th>{{ __('qc.ncrs.col_disposition') }}</th>
            <th>{{ __('qc.ncrs.col_severity') }}</th>
            <th style="text-align: right;">{{ __('qc.ncrs.col_status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ncrs as $ncr)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--danger);">
                {{ $ncr['ncr_no'] }}
              </td>
              <td>
                <span class="mono" style="font-weight: 600; color: var(--primary-text);">{{ $ncr['ssc_id'] }}</span>
                <span style="font-size: 0.76rem; color: var(--text-subtle); display: block;">{{ $ncr['part_name'] }}</span>
              </td>
              <td style="font-weight: 500; color: var(--text-main);">{{ $ncr['customer'] }}</td>
              <td style="max-width: 280px; font-size: 0.82rem; color: var(--text-muted);">
                {{ $ncr['issue_description'] }}
              </td>
              <td style="max-width: 250px; font-size: 0.82rem; color: var(--text-main);">
                <strong>{{ $ncr['disposition'] }}</strong>
                <span style="font-size: 0.74rem; color: var(--text-subtle); display: block;">PIC: {{ $ncr['pic_engineer'] }}</span>
              </td>
              <td>
                <span class="badge {{ $ncr['severity'] === 'Kritis' ? 'badge-red' : 'badge-orange' }}">
                  {{ $ncr['severity'] }}
                </span>
              </td>
              <td style="text-align: right;">
                <span class="badge {{ $ncr['status'] === 'Resolved / Closed' ? 'badge-green' : 'badge-purple' }}">
                  {{ $ncr['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

