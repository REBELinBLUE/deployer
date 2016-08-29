import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';

const EmailList = (props) => {
  const {
    emails,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('notifyEmails.create'),
    edit: Lang.get('notifyEmails.edit'),
    label: Lang.get('notifyEmails.label'),
    none: Lang.get('notifyEmails.none'),
    name: Lang.get('notifyEmails.name'),
    email: Lang.get('notifyEmails.email'),
  };

  if (emails.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const emailList = [];
  emails.forEach((email) => {
    const id = `email_${email.id}`;

    emailList.push(
      <tr key={id} id={id}>
        <td>{email.name}</td>
        <td>{email.email}</td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(email)}><Icon fa="edit" /></Button>
          </ButtonGroup>
        </td>
      </tr>
    );
  });

  return (
    <Box title={strings.label} onAdd={onAdd} create={strings.create} table>
      <table className="table table-striped">
        <thead>
          <tr>
            <th>{strings.name}</th>
            <th>{strings.email}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{emailList}</tbody>
      </table>
    </Box>
  );
};

EmailList.propTypes = {
  emails: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default EmailList;
