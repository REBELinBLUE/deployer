import React, { PropTypes } from 'react';
import { Button, Modal, ModalHeader, ModalBody, ModalTitle, ModalFooter } from 'react-bootstrap';

import Icon from '../../app/components/Icon';

const Dialog = (props) => {
  const {
    project,
    visible,
    onHide,
  } = props;

  const strings = {
    public_key: Lang.get('projects.public_ssh_key'),
    server_keys: Lang.get('projects.server_keys'),
    git_keys: Lang.get('projects.git_keys'),
    close: Lang.get('app.close'),
  };

  return (
    <Modal show={visible} onHide={onHide} id="key">
      <ModalHeader closeButton>
        <ModalTitle>
          <Icon fa="key" /> {strings.public_key}
        </ModalTitle>
      </ModalHeader>
      <ModalBody>
        <div className="alert alert-warning">
          <p>{strings.server_keys}</p>
          <p>{strings.git_keys}</p>
        </div>
        <pre>{project.public_key}</pre>
      </ModalBody>
      <ModalFooter>
        <Button className="pull-right" bsStyle="default" onClick={onHide}>{strings.close}</Button>
      </ModalFooter>
    </Modal>
  );
};

Dialog.propTypes = {
  project: PropTypes.object.isRequired,
  onHide: PropTypes.func.isRequired,
  visible: PropTypes.bool,
};

Dialog.defaultProps = {
  visible: false,
};

export default Dialog;
