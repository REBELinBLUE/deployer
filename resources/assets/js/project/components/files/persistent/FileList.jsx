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
    create: Lang.get('sharedFiles.create'),
    edit: Lang.get('sharedFiles.edit'),
    label: Lang.get('sharedFiles.label'),
    none: Lang.get('sharedFiles.none'),
    name: Lang.get('sharedFiles.name'),
    file: Lang.get('sharedFiles.file'),
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
    const id = `file_${file.id}`;

    fileList.push(
      <tr key={id} id={id}>
        <td>{file.name}</td>
        <td>{file.file}</td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className=" btn-edit" title={strings.edit} onClick={() => onEdit(file)}>
              <Icon fa="edit" />
            </Button>
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
            <th>{strings.file}</th>
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
