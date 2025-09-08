jQuery(function($){
  $('.sh-day-closed').on('change', function(){
    var row = $(this).closest('tr');
    row.find('input[type=time]').prop('disabled', $(this).is(':checked'));
  });

  function toggleSecond(){
    var show = $('#sh_enable_second_hours').is(':checked');
    $('.sh-second-hours').toggle(show);
  }
  toggleSecond();
  $('#sh_enable_second_hours').on('change', toggleSecond);

  $('#sh-add-holiday').on('click', function(e){
    e.preventDefault();
    var table = $('#sh-holidays');
    var index = table.find('tr').length - 1;
    var row = '<tr>'+
      '<td><input type="date" name="sh_holiday_overrides['+index+'][from]" /></td>'+
      '<td><input type="date" name="sh_holiday_overrides['+index+'][to]" /></td>'+
      '<td><input type="text" name="sh_holiday_overrides['+index+'][label]" /></td>'+
      '<td><input type="checkbox" name="sh_holiday_overrides['+index+'][closed]" class="sh-holiday-closed" /></td>'+
      '<td><input type="time" name="sh_holiday_overrides['+index+'][open]" /></td>'+
      '<td><input type="time" name="sh_holiday_overrides['+index+'][close]" /></td>'+
      '<td><button class="button sh-remove-holiday">Remove</button></td>'+
    '</tr>';
    table.append(row);
  });
  $(document).on('click', '.sh-remove-holiday', function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
  });
});