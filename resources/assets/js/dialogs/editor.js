import React, { PropTypes } from 'react';
import { bindActionCreators } from 'redux';
import { reduxForm } from 'redux-form';

import * as constants from './constants';
import { hideDialog, saveObject } from './actions';

// FIXME: How do we validate that both are set?
// Maybe do the same as redux form and wrap in yet another component with 2 isRequired props?
// https://github.com/erikras/redux-form/blob/master/src/createReduxFormConnector.js
export default ({ dialog, fields }) => (
  (WrappedDialogComponent) => {
    const DialogContainer = (props) => {
      const {
        actions,
        ...others,
      } = props;

      return (
        <WrappedDialogComponent
          onHide={actions.hideDialog}
          {...others}
        />
      );
    };

    DialogContainer.propTypes = {
      actions: PropTypes.object.isRequired,
    };

    const mapStateToProps = (state) => ({
      initialValues: state.getIn([constants.NAME, 'instance']).toJS(),
      visible: (state.getIn([constants.NAME, 'visible']) === dialog),
    });

    const mapDispatchToProps = (dispatch) => ({
      actions: bindActionCreators({
        hideDialog: () => (hideDialog(dialog)),
      }, dispatch),
    });

    return reduxForm({
      form: dialog,
      fields,
      onSubmit: saveObject,
      getFormState: (state) => state.get('form').toJS(),
    }, mapStateToProps, mapDispatchToProps)(DialogContainer);
  }
);
