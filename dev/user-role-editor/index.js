import App from './App';
import { render } from '@wordpress/element';
import { AppProvider } from './context';

render(
  <AppProvider>
    <App />
  </AppProvider>,
  document.getElementById('rolemaster-suite-user--role--editor-root')
);
