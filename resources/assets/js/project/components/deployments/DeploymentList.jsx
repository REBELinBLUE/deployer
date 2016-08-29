import React, { PropTypes } from 'react';
import { ButtonGroup } from 'react-bootstrap';
import { Link } from 'react-router';

import Icon from '../../../app/components/Icon';
import Box from '../../../app/components/Box';
import DateTime from '../../../app/components/DateTime';
import Label from './DeploymentLabel';
import Reason from './DeploymentReason';
import OptionalLink from './OptionalLink';
import Pagination from './Pagination';

const DeploymentList = (props) => {
  const {
    data,
    ...others,
  } = props;

  const strings = {
    label: Lang.get('deployments.label'),
    none: Lang.get('deployments.none'),
    date: Lang.get('app.date'),
    started: Lang.get('deployments.started_by'),
    deployer: Lang.get('deployments.deployer'),
    committer: Lang.get('deployments.committer'),
    commit: Lang.get('deployments.commit'),
    branch: Lang.get('deployments.branch'),
    status: Lang.get('app.status'),
    details: Lang.get('app.details'),
  };

  if (data.length === 0) {
    return (
      <Box title={strings.label} create={strings.create}>
        <p>{strings.none}</p>
      </Box>
    );
  }

  const deploymentList = [];
  data.forEach((deployment) => {
    const id = `deployment_${deployment.id}`;

    deploymentList.push(
      <tr key={id} id={id}>
        <td><DateTime date={deployment.started_at} /></td>
        <td><Reason isWebhook={deployment.is_webhook} reason={deployment.reason} /></td>
        <td><OptionalLink to={deployment.build_url}>{deployment.deployer_name}</OptionalLink></td>
        <td>{deployment.committer}</td>
        <td><OptionalLink to={deployment.commit_url}>{deployment.short_commit}</OptionalLink></td>
        <td>
          <OptionalLink to={deployment.branch_url}>
            <span className="label label-default">{deployment.branch}</span>
          </OptionalLink>
        </td>
        <td><Label status={deployment.status} /></td>
        <td>
          <ButtonGroup className="pull-right">
            <Link
              to={`/projects/${deployment.project_id}/deployments/${deployment.id}`}
              type="button"
              className="btn btn-default"
              title={strings.details}
            >
              <Icon fa="info-circle" />
            </Link>
          </ButtonGroup>
        </td>
      </tr>
    );
  });

  /*
  BUTTONS
   @if ($deployment->isSuccessful())
   <button type="button" data-toggle="modal" data-backdrop="static" data-target="#redeploy" data-optional-commands="{{ $deployment->optional_commands_used }}" data-deployment-id="{{ $deployment->id }}" class="btn btn-default btn-rollback @if ($deployment->isCurrent()) hide @endif" title="{{ Lang::get('deployments.rollback') }}"><i class="fa fa-cloud-upload"></i></button>
   @endif

   @if ($deployment->isPending() || $deployment->isRunning())
   <!-- FIXME: Try and change this to a form as abort should be a POST request -->
   <a href="{{ route('deployments.abort', ['id' => $deployment->id]) }}" class="btn btn-default btn-cancel" title="{{ Lang::get('deployments.cancel') }}"><i class="fa fa-ban"></i></a>
   @endif
   */

  return (
    <Box title={strings.label} create={strings.create} table>
      <table className="table table-striped">
        <thead>
          <tr>
            <th>{strings.date}</th>
            <th>{strings.started}</th>
            <th>{strings.deployer}</th>
            <th>{strings.committer}</th>
            <th>{strings.commit}</th>
            <th>{strings.branch}</th>
            <th>{strings.status}</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>{deploymentList}</tbody>
      </table>
      <Pagination {...others} />
    </Box>
  );
};

DeploymentList.propTypes = {
  data: PropTypes.array.isRequired,
};

export default DeploymentList;
