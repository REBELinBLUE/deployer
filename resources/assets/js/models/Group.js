import { PropTypes } from 'react';

// Reusable react shape for Group model
export default PropTypes.shape({
  id: PropTypes.number.isRequired,
  name: PropTypes.string.isRequired,
  order: PropTypes.number.isRequired,
});
