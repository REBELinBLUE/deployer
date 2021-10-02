import $ from 'jquery';
import 'select2';

const filterAuditLog = () => {
  $('#filter_log').submit();
};

const sortDropdown = (a, b) => {
  // Ensure the "All" items are stuck at the top
  if (a.value === '') {
    return -1;
  } else if (b.value === '') {
    return 1;
  }

  if (a.text > b.text) {
    return 1;
  } else if (a.text < b.text) {
    return -1;
  }

  return 0;
};

// FIXME: Convert to class
export default () => {
  const selectOptions = {
    width: '175px',
    minimumResultsForSearch: Infinity,
    sorter: data => data.sort(sortDropdown),
  };

  $('#filter_type').select2(selectOptions);
  $('#filter_id').select2(selectOptions);

  $('#filter_type').on('change', () => {
    $('#filter_id').val('');
    filterAuditLog();
  });

  $('#filter_id').on('change', filterAuditLog);
};
