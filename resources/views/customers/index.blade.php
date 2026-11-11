@extends('layouts.app')

@section('title', __('qc.customers.title'))
@section('page_title', __('qc.customers.page_title'))

@section('top_actions')
  <button class="btn btn-primary btn-sm" onclick="window.qcAlert({ title: @json(__('qc.common.info')), message: @json(__('qc.customers.next_release')), variant: 'info' })">
    <i data-lucide="plus-circle"></i> {{ __('qc.customers.add_btn') }}
  </button>
@endsection

@section('content')
  <div class="page-header">
    <div class="page-title">
      <h1>{{ __('qc.customers.heading') }}</h1>
      <p>{{ __('qc.customers.subtitle') }}</p>
    </div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="qc-table">
        <thead>
          <tr>
            <th>{{ __('qc.customers.col_code') }}</th>
            <th>{{ __('qc.customers.col_name') }}</th>
            <th>{{ __('qc.customers.col_sector') }}</th>
            <th>{{ __('qc.customers.col_pic') }}</th>
            <th>{{ __('qc.customers.col_jobs') }}</th>
            <th style="text-align: right;">{{ __('qc.customers.col_class') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($customers as $c)
            <tr>
              <td class="mono" style="font-weight: 700; color: var(--primary-text);">
                {{ $c['code'] }}
              </td>
              <td>
                <strong style="color: var(--text-main);">{{ $c['name'] }}</strong>
              </td>
              <td>
                {{ $c['industry'] }}
                <span style="font-size: 0.76rem; color: var(--text-subtle); display: block;">{{ __('qc.customers.location') }} {{ $c['location'] }}</span>
              </td>
              <td>
                <span style="font-weight: 600;">{{ $c['pic_name'] }}</span>
                <span style="font-size: 0.76rem; color: var(--text-subtle); display: block;" class="mono">{{ $c['pic_phone'] }}</span>
              </td>
              <td>
                <span class="badge badge-blue">{{ __('qc.customers.active_jobs', ['count' => $c['active_jobs']]) }}</span>
              </td>
              <td style="text-align: right;">
                <span class="badge {{ $c['status'] === 'Akun Prioritas' ? 'badge-purple' : 'badge-green' }}">
                  {{ $c['status'] }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

