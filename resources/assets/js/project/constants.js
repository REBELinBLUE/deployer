export const NAME = 'project';

// Possible server statuses
export const SERVER_STATUS_SUCCESSFUL = 0;
export const SERVER_STATUS_UNTESTED = 1;
export const SERVER_STATUS_FAILED = 2;
export const SERVER_STATUS_TESTING = 3;

// Possible deployment statuses
export const DEPLOY_STATUS_COMPLETED = 0;
export const DEPLOY_STATUS_PENDING = 1;
export const DEPLOY_STATUS_DEPLOYING = 2;
export const DEPLOY_STATUS_FAILED = 3;
export const DEPLOY_STATUS_COMPLETED_WITH_ERRORS = 4;
export const DEPLOY_STATUS_ABORTING = 5;
export const DEPLOY_STATUS_ABORTED = 6;

// Possible heartbeat statuses
export const HEARTBEAT_STATUS_OK = 0;
export const HEARTBEAT_STATUS_UNTESTED = 1;
export const HEARTBEAT_STATUS_MISSING = 2;

// Possible link statuses
export const LINK_STATUS_SUCCESS = 0;
export const LINK_STATUS_FAILED = 1;

// Possible execution steps
export const STEP_BEFORE_CLONE = 1;
export const STEP_DO_CLONE = 2;
export const STEP_AFTER_CLONE = 3;
export const STEP_BEFORE_INSTALL = 4;
export const STEP_DO_INSTALL = 5;
export const STEP_AFTER_INSTALL = 6;
export const STEP_BEFORE_ACTIVATE = 7;
export const STEP_DO_ACTIVATE = 8;
export const STEP_AFTER_ACTIVATE = 9;
export const STEP_BEFORE_PURGE = 10;
export const STEP_DO_PURGE = 11;
export const STEP_AFTER_PURGE = 12;
