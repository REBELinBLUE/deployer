import dialogTests from './dialog.spec';
import formattersTests from './formatters.spec';
import localizationTests from './localization.spec';
import currentProjectTests from './projectId.spec';
import targetTests from './target.spec';

describe('Utils', () => {
  dialogTests();
  formattersTests();
  localizationTests();
  currentProjectTests();
  targetTests();
});
