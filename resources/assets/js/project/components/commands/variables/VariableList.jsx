import React, { PropTypes } from 'react';
import { Button, ButtonGroup } from 'react-bootstrap';

import Icon from '../../../../app/components/Icon';
import Box from '../../../../app/components/Box';

const Variables = (props) => {
  const {
    variables,
    onAdd,
    onEdit,
  } = props;

  const strings = {
    create: Lang.get('variables.create'),
    label: Lang.get('variables.label'),
    description: Lang.get('variables.description'),
    example: Lang.get('variables.example'),
    name: Lang.get('variables.name'),
    value: Lang.get('variables.value'),
    edit: Lang.get('variables.edit'),
  };

  const header = (
    <div className="box-body">
      <p dangerouslySetInnerHTML={{ __html: strings.description }} />
      <p dangerouslySetInnerHTML={{ __html: strings.example }} />
    </div>
  );

  if (variables.length === 0) {
    return (
      <Box title={strings.label} onAdd={onAdd} create={strings.create} header={header}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const variableList = [];
  variables.forEach((variable) => {
    const id = `variable_${variable.id}`;

    variableList.push(
      <tr key={id} id={id}>
        <td>{variable.name}</td>
        <td>{variable.value}</td>
        <td>
          <ButtonGroup className="pull-right">
            <Button className="btn-edit" title={strings.edit} onClick={() => onEdit(variable)}>
              <Icon fa="edit" />
            </Button>
          </ButtonGroup>
        </td>
      </tr>
    );
  });

  return (
    <Box title={strings.label} onAdd={onAdd} create={strings.create} header={header} table>
      <table className="table table-striped">
        <thead>
          <tr>
            <th>{strings.name}</th>
            <th>{strings.value}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{variableList}</tbody>
      </table>
    </Box>
  );
};

Variables.propTypes = {
  variables: PropTypes.array.isRequired,
  onEdit: PropTypes.func.isRequired,
  onAdd: PropTypes.func.isRequired,
};

export default Variables;
