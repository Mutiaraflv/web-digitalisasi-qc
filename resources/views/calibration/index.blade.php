@extends('layouts.app')

@section('title', __('qc.calibration.title'))
@section('page_title', __('qc.calibration.page_title'))

@section('top_actions')
  <button class="btn btn-primary btn-sm" onclick="window.qcAlert({ title: @json(__('qc.common.info')), message: @json(__('qc.calibration.next_release')), variant: 'info' })">
    <i data-lucide="plus-circle"></i> {{ __('qc.calibration.add_btn') }}
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.calibration.heading') }}</h1>
      <p>{{ __('qc.calibration.subtitle') }}</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>{{ __('qc.calibration.col_code') }}</th>
            <th>{{ __('qc.calibration.col_name') }}</th>
            <th>{{ __('qc.calibration.col_brand') }}</th>
            <th>{{ __('qc.calibration.col_resolution') }}</th>
            <th>{{ __('qc.calibration.col_last') }}</th>
            <th>{{ __('qc.calibration.col_valid') }}</th>
            <th style="text-align: right;">{{ __('qc.calibration.col_status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tools as $t)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--primary-text);">
                {{ $t['tool_code'] }}
              </td>
              <td>
                <strong style="color: var(--text-main);">{{ $t['name'] }}</strong>
                <span style="font-size: 0.74rem; color: var(--text-subtle); display: block;" class="mono">{{ __('qc.calibration.cert') }} {{ $t['cert_no'] }}</span>
              </td>
              <td>{{ $t['brand'] }}</td>
              <td class="mono" style="font-size: 0.8rem; font-weight: 600;">{{ $t['resolution'] }}</td>
              <td class="mono">{{ $t['last_calibrated'] }}</td>
              <td class="mono" style="font-weight: 600; color: var(--success);">{{ $t['valid_until'] }}</td>
              <td style="text-align: right;">
                <span class="badge badge-green">
                  <i data-lucide="check-circle-2" style="width: 12px; height: 12px;"></i> {{ $t['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

