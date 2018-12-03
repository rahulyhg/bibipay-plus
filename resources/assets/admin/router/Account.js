import Main from '../component/Account/Main';
import AccountUesrs from  '../component/Account/AccountUsers';
import AccountUsersDeteail from  '../component/Account/AccountUsersdeteail';
import AccountHome from '../component/Account/AccountHome';
import AccountDetails from '../component/Account/AccountDetails';
import AccountScale from '../component/Account/AccountScale';
import AccountOrder from '../component/Account/AccountOrder';
import AccountRelease from '../component/Account/AccountRelease';
import AccountReview from '../component/Account/AccountReview';
import AccountTermReview from '../component/Account/AccountTermReview';
import AccountCost from '../component/Account/AccountCost';
import AccountList from '../component/Account/AccountList';
import AccountView from '../component/Account/AccountView';
import AccountDraft from '../component/Account/AccountDraft';

export default {
    path: 'account/users',
    component: Main,
      children: [
          { path: '/account/users', component: AccountUesrs },
          { path: '/account/users/deteail', component: AccountUsersDeteail },
          { path: '/account/Home', component: AccountHome },
          { path: '/Details/:id', component: AccountDetails },
          { path: '/Order', component: AccountOrder },
          { path: '/Scale', component: AccountScale },
          { path: '/Release', component: AccountRelease },
          { path: '/review', component: AccountReview },
          { path: '/TermReview', component: AccountTermReview },
          { path: '/cost', component: AccountCost },
          { path: '/Draft', component: AccountDraft },
          { path: '/List/:id', component: AccountRelease },
          { path: '/View/:id', component: AccountView },
          { path: '/List', component: AccountList }
      ],
  };