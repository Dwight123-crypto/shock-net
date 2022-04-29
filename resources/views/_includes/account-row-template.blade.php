<?php 
    $value = (object)[];
    $value->rate = 0;
    $value->code = '';
    $value->tax_id = 0;
    $value->discount_id = 0;
    $value->ref_number = '';
    $value->debit = '0.00';
    $value->credit = '0.00';
    
    if($select['value']) {
        $data = $select['options'][ $select['value'] ];
        if( isset($data->rate) ) $value->rate = $data->rate;
        if( isset($data->code) ) $value->code = $data->code;
        if( isset($data->tax_id) ) $value->tax_id = $data->tax_id;
        if( isset($data->discount_id) ) $value->discount_id = $data->discount_id;
    }
    
    $value->id = '0';
    
    if(!empty($voucher)) {
        // dd( 'DEBIT', $voucher->toArray() );
        if( isset($voucher->ref_number) ) $value->ref_number = $voucher->ref_number;
        if( isset($voucher->debit) ) $value->debit = $voucher->debit;
        if( isset($voucher->credit) ) $value->credit = $voucher->credit;
        if( isset($voucher->tax_id) ) $value->tax_id = $voucher->tax_id;
        if( isset($voucher->discount_id) ) $value->discount_id = $voucher->discount_id;
        if( isset($voucher->rate) ) $value->rate = $voucher->rate;
        if( isset($voucher->id) ) $value->id = $voucher->id;
    }
    
    $force_show = false;
    if(strpos($account_row_class, 'optional') !== false) {
        if(floatval($value->debit) || floatval($value->credit))
            $force_show = true;
    }
    // print_r( $value ); echo '<br>';
?>
<tr class="account-row {{ $entry_type }} {{ $account_row_class }}"@if($force_show) style="display:table-row" @endif>
  <td class="account-number"> 
    <input type="hidden" class="rate" value="{{ $value->rate }}" name="{{ $parent_name }}[rate]" />
    <input type="text" name="{{ $parent_name }}[code]" value="{{ $value->code }}" class="form-control input-sm {{ $entry_type }} code" readonly /> 
  </td>
  <td class="account-title"> 
    {!! custom_form_select(
          $parent_name . '[chart_account_id]', // name
          $select['value'],                    // value
          ['class' => 'form-control input-sm chart-account-dropdown'], // attribs
          $select['options'],                  // the options in array
          $select['data_fields'],              // the data-fields in each <option>
          false
      ) !!}
  </td>
  <td class="ref-number"> 
    <input type="hidden" class="tax_id" value="{{ $value->tax_id }}" name="{{ $parent_name }}[tax_id]" /> 
    <input type="hidden" class="discount_id" value="{{ $value->discount_id }}" name="{{ $parent_name }}[discount_id]" /> 
    <input type="text" name="{{ $parent_name }}[ref_number]" value="{{ $value->ref_number }}" class="form-control input-sm {{ $entry_type }} ref_number" readonly />
  </td>
  @if($entry_type == 'debit')
  <td class="debit">  <input type="text" name="{{ $parent_name }}[debit]" value="{{ number_format($value->debit, 2, '.', '') }}" class="form-control input-sm debit_field" readonly /> </td>
  <td class="credit"> <input type="hidden" value="0" name="{{ $parent_name }}[credit]" class="credit_field" /> </td>
  @else
  <td class="debit">  <input type="hidden" value="0" name="{{ $parent_name }}[debit]" class="debit_field" /> </td>
  <td class="credit"> <input type="text" name="{{ $parent_name }}[credit]" value="{{ number_format($value->credit, 2, '.', '') }}" class="form-control input-sm credit_field" readonly /> </td>
  @endif
  <input type="hidden" value="{{ $key }}" name="{{ $parent_name }}[key]" class="key" />
  <input type="hidden" value="{{ $value->id }}" name="{{ $parent_name }}[id]" class="v_id" />
</tr>
