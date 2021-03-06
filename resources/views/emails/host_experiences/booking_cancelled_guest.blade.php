@extends('emails.template')
@section('emails.main')
<div style="font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:15px;max-width:600px;margin:0 auto;display:block;text-align:center;padding-right:5px;padding-bottom:5px;padding-top:0px;padding-left:5px;color:#ffffff;background-color:#ffaa91">
  <div style="font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:15px;max-width:600px;margin:0 auto;display:block;padding-left:5px;padding-right:5px;padding-bottom:5px;padding-top:0px">
    <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;padding-top:56.25px;width:100%" width="100%" cellpadding="0" cellspacing="0">
      <tbody>
        <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
          <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif" valign="top" align="center">
            <a href="{{ $url }}" title="{{ $site_name }}" style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#ff5a5f;text-decoration:none" target="_blank">
              <img src="{{ EMAIL_LOGO_URL }}" alt="{{ $site_name }}" style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;max-width:100%;border:0;margin:0;display:block" width="160" border="0" class="CToWUd">
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div width="50%" style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif" align="center">
  </div>
  <div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding-top:25px;padding-bottom:56.25px">
    @if($result['cancelled_by'] != 'Guest')
    <h1 style="font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-weight:bold;padding-bottom:10px;text-align:center;font-size:30px;line-height:35px;margin:0;padding:0;letter-spacing:-1px;color:#ffffff">
      {{ trans('messages.email.booking_cancelled_by',[], null, $locale) }}  {{ $result['host_users']['first_name'] }}
    </h1>
    @else
    <h1 style="font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-weight:bold;padding-bottom:10px;text-align:center;font-size:30px;line-height:35px;margin:0;padding:0;letter-spacing:-1px;color:#ffffff">
      {{ trans('experiences.emails.you_have_cancelled_a_booking',['name' => $result['host_experiences']['title'], 'dates' => $result['checkin_md']], null, $locale) }}
    </h1>
    @endif
    <p style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;text-align:center;font-size:22px;padding:18px 15px 0 15px;padding-top:18px;font-weight:normal;line-height:32px">
      {{ trans('experiences.emails.experience_with_host',['count' => 1, 'first_name' => $result['host_users']['first_name']], null, $locale) }} 
    </p>
  </div>
</div>
<div style="font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:15px;max-width:600px;margin:0 auto;display:block;background-color:#ffffff;padding-right:5px;padding-bottom:5px;padding-top:0px;padding-left:5px">
  <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
    <tbody style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
        <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif" valign="top">
          <div style="padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding-top:37.5px;margin:0 auto;padding-left:30px;padding-right:30px;max-width:500px">
            <div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
              <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
                <tbody>
                  <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                    <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                    </td>
                    <td style="padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block!important;margin:0 auto!important;clear:both!important;max-width:610px!important">
                      <div style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:30px;border-style:solid;color:#565a5c;border-color:#dbdbdb!important;border-width:1px!important">
                        <div style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;font-size:22px;line-height:32px">
                          {{ @$result['messages']['message'] }} 
                        </div>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%;font-size:0;line-height:0" width="100%" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:0" height="30">
                                &nbsp;
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              </td>
                              <td style="padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block!important;margin:0 auto!important;clear:both!important;max-width:610px!important">
                                <div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                  <img alt="" src="{{ $result['host_experiences']['photo_name'] }}" style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;max-width:100%" width="100%" class="CToWUd">
                                </div>
                              </td>
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%;font-size:0;line-height:0" width="100%" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:0" height="30">
                                &nbsp;
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              </td>
                              <td style="padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block!important;margin:0 auto!important;clear:both!important;max-width:610px!important">
                                <div style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;font-size:22px">
                                  <h3 style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#565a5c;line-height:24px;font-weight:bold;font-size:22px;padding-bottom:3px!important">{{ $result['host_experiences']['title'] }}
                                  </h3>
                                  <p style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;color:#565a5c;padding-top:5px">
                                    <span style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;white-space:nowrap">
                                      {{ $result['number_of_guests'] }} 
                                      {{ trans_choice('messages.home.guest',$result['number_of_guests'], [], null, $locale) }}
                                    </span>
                                    ???
                                    <span style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;white-space:nowrap">
                                      {{ $result['duration_text'] }}
                                    </span>
                                    <!-- ??? -->
                                    <span style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;white-space:nowrap">
                                    </span>
                                  </p>
                                </div>
                              </td>
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%;font-size:0;line-height:0" width="100%" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:0" height="25">
                                &nbsp;
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;width:100%">
                          <tbody>
                            <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                              </td>
                              <td style="padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block!important;margin:0 auto!important;clear:both!important;max-width:610px!important">
                                <div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                  <table style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:150%;border-spacing:0;font-size:22px;width:auto!important">
                                    <tbody>
                                      <tr style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                        <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:32px;width:auto!important">
                                          <strong style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">{{ trans('messages.home.checkin',[], null, $locale) }}
                                          </strong>
                                          <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                          <span style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;font-weight:normal;line-height:2em;padding-bottom:3px">
                                            <a style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#565a5c;text-decoration:none">{{ $result['checkin_mdy'] }}
                                            </a>
                                          </span>
                                          {{--
                                            <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                            <span style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;color:#9ca299;line-height:100%;font-size:14px;font-size:14px">
                                              {{ trans('messages.your_reservations.flexible_checkin_time',[], null, $locale) }}
                                            </span>
                                            --}}
                                          </td>
                                          <td style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:32px;padding:0 30px;min-width:11px!important;min-height:21px!important;width:11px!important;height:21px!important">
                                            <img alt="-" src="{{ $url.('images/caret_right_11x21.png') }}" valign="middle" style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;max-width:100%" class="CToWUd">
                                          </td>
                                          <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;line-height:32px;width:auto!important">
                                            <strong style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">{{ trans('messages.your_reservations.checkout',[], null, $locale) }}
                                            </strong>
                                            <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                            <span style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;font-weight:normal;line-height:2em;padding-bottom:3px">
                                              <a style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;color:#565a5c;text-decoration:none">{{ $result['checkout_mdy'] }}
                                              </a>
                                            </span>
                                            {{--
                                              <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                              <span style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;color:#9ca299;line-height:100%;font-size:14px;font-size:14px">
                                                {{ trans('messages.your_reservations.flexible_checkout_time',[], null, $locale) }}
                                              </span>
                                              --}}
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </td>
                                  <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </td>
                        <td style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                
                @include('emails.common.exp_reservation_price_details', ['result' => $result, 'to' => 'guest'])

                <div style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;padding:0;margin-top:31.25px">
                  <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">{{trans('experiences.emails.we_have_issued_refund_back_in_3_5_days')}}
                  </p>
                  <br>
                  <p style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;font-size:18px;line-height:32px;color:#565a5c">{{trans('experiences.emails.we_sorry_this_not_worK_this_time_you_can_try_different')}}
                  </p>
                  <br>
                  <a href="{{ $url.('experiences/'.$result['host_experiences']['id']) }}" style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block;text-decoration:none;border-radius:2px;border:1px solid;text-align:center;vertical-align:middle;font-weight:bold;white-space:nowrap;background:#ffffff;background-color:#007a87;color:#ffffff;border-top-width:1px;border-color:transparent;font-size:22px;padding:16px" target="_blank">
                    {{ trans('experiences.emails.choose_another_time',[], null, $locale) }}
                  </a>
                  <br>
                  <a href="{{ $url.('s?current_refinement=Experiences&location='.$result['host_experiences']['city_details']['name']) }}" style="margin:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;display:block;text-decoration:none;border-radius:2px;border:1px solid #007a87 !important;text-align:center;vertical-align:middle;font-weight:bold;white-space:nowrap;color:#000000;border-top-width:1px;border-color:transparent;font-size:22px;padding:16px" target="_blank">
                    {{ trans('experiences.emails.show_more_experience_in_city',['city' => $result['host_experiences']['city_details']['name']], null, $locale) }}
                  </a>
                </div>
              </div>
              <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
              <br style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    @stop