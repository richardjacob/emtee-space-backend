<div style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;margin-top:50px">
  <hr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;background-color:#dbdbdb;min-height:1px;border:none">
  <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%;font-size:0;line-height:0" width="100%" cellpadding="0" cellspacing="0">
    <tbody>
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:0" height="60">
          &nbsp;
        </td>
      </tr>
    </tbody>
  </table>
  <h2 style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-weight:bold;line-height:28px;padding-bottom:10px;font-size:26px;color:#565a5c">
    {{ trans('messages.email.price_details',[], null, $locale) }}
  </h2>
  <table style="display:none;margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%;font-size:0;line-height:0" width="100%" cellpadding="0" cellspacing="0">
    <tbody>
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:0" height="40">
          &nbsp;
        </td>
      </tr>
    </tbody>
  </table>
  <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
    <tbody>
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="text-transform: capitalize; margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans_choice('messages.booking.hour',2, [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ $result['hours'] }}
          </p>
        </td>
      </tr>
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['base_per_hour'] }} x {{ $result['hours'] }} {{ trans_choice('messages.booking.hour',$result['hours'], [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['base_per_hour'] * $result['hours'] }}
          </p>
        </td>
      </tr>
      @if($result['cleaning'] > 0)
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans('messages.your_reservations.cleaning_fee', [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['cleaning'] }}
          </p>
        </td>
      </tr>
      @endif
      @if($result['security'] > 0)
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans('messages.your_reservations.security_fee', [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['security'] }}
          </p>
        </td>
      </tr>
      @endif
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans('messages.your_reservations.subtotal', [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['subtotal'] }}
          </p>
        </td>
      </tr>
      @if($to == 'admin' || $to == 'guest')
      @if($result['service'] > 0)
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans('messages.your_reservations.service_fee', [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['service'] }}
          </p>
        </td>
      </tr>
      @endif
      @endif

      @if($to == 'admin' || $to == 'host')
      @if($result['host_fee'] > 0)
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ trans('messages.your_reservations.host_fee', [], null, $locale) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            -{{ html_string($result['currency']['original_symbol']) }}{{ $result['host_fee'] }}
          </p>
        </td>
      </tr>
      @endif
      @endif

       @if($to == 'host')
      @if(@$results['hostPayouts']['total_penalty_amount'] > 0)
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ ucfirst(trans('messages.your_reservations.subtracted_penalty_amount', [], null, $locale)) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{@$results['hostPayouts']['total_penalty_amount'] }}
          </p>
        </td>
      </tr>
      @endif
      @endif

      @if($to == 'admin' || $to == 'guest')
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ ucfirst(trans('messages.your_reservations.total', [], null, $locale)) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['total'] }}
          </p>
        </td>
      </tr>
      @endif
      @if($to == 'host' && $result['paymode'])
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">
            {{ ucfirst(trans('messages.your_reservations.total_payout', [], null, $locale)) }}
          </p>
        </td>
        <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;padding-left:30px" valign="top">
          <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c; text-align: right;">
            {{ html_string($result['currency']['original_symbol']) }}{{ $result['host_payout'] }}
          </p>
        </td>
      </tr>
      @endif
      @stack('extra_prices')
    </tbody>
  </table>
</div>
