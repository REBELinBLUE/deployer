import React, { PropTypes } from 'react';
import { FormGroup as BootstrapFormGroup, FormControl } from 'react-bootstrap';

import FieldError from './FieldError';

const FormGroup = (props) => {
  const {
    touched,
    error,
    children,
    ...others,
  } = props;

  return (
    <BootstrapFormGroup validationState={touched && error ? 'error' : null} {...others}>
      {children}
      <FieldError touched={touched} error={error} {...others} />
      <FormControl.Feedback />
    </BootstrapFormGroup>
  );
};

FormGroup.propTypes = {
  touched: PropTypes.bool.isRequired,
  error: PropTypes.string.isRequired,
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node,
  ]).isRequired,
};

export default FormGroup;
