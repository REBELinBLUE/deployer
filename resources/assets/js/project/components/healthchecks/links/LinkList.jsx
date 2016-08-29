import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';
import Label from './LinkLabel';

const LinkList = (props) => {
  const {
    links,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('checkUrls.create'),
    edit: Lang.get('checkUrls.edit'),
    label: Lang.get('checkUrls.label'),
    none: Lang.get('checkUrls.none'),
    title: Lang.get('checkUrls.title'),
    url: Lang.get('checkUrls.url'),
    frequency: Lang.get('checkUrls.frequency'),
    last_status: Lang.get('checkUrls.last_status'),
    minutes: Lang.get('checkUrls.minutes'),
  };

  if (links.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const linksList = [];
  links.forEach((link) => {
    const id = `link_${link.id}`;

    linksList.push(
      <tr key={id} id={id}>
        <td>{link.title}</td>
        <td>{link.url}</td>
        <td>{link.period} {strings.minutes}</td>
        <td><Label status={link.last_status} /></td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(link)}><Icon fa="edit" /></Button>
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
            <th>{strings.title}</th>
            <th>{strings.url}</th>
            <th>{strings.frequency}</th>
            <th>{strings.last_status}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{linksList}</tbody>
      </table>
    </Box>
  );
};

LinkList.propTypes = {
  links: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default LinkList;
