import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';

const FileList = (props) => {
  const {
    files,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('projectFiles.create'),
    edit: Lang.get('projectFiles.edit'),
    view: Lang.get('projectFiles.view'),
    label: Lang.get('projectFiles.label'),
    none: Lang.get('projectFiles.none'),
    name: Lang.get('projectFiles.name'),
    path: Lang.get('projectFiles.path'),
  };

  if (files.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const fileList = [];
  files.forEach((file) => {
    const id = `config_${file.id}`;

    fileList.push(
      <tr key={id} id={id}>
        <td>{file.name}</td>
        <td>{file.path}</td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-view" title={strings.view}><Icon fa="eye" /></Button>
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(file)}><Icon fa="edit" /></Button>
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
            <th>{strings.path}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{fileList}</tbody>
      </table>
    </Box>
  );
};

FileList.propTypes = {
  files: PropTypes.array.isRequired,
  onAdd: PropTypes.func.isRequired,
  onEdit: PropTypes.func.isRequired,
};

export default FileList;
