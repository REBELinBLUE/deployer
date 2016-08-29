import Immutable from 'immutable';
import { reducer as formReducer } from 'redux-form';

const initialState = Immutable.fromJS({});

export default function (state = initialState, action) {
  return Immutable.fromJS(formReducer(state.toJS(), action));
}
