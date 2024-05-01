

import App from "./App";
import { createRoot } from "@wordpress/element";
import { AppProvider } from "./context";

const domNode = document.getElementById("rolemaster-suite-user--role--editor-root");

const root = createRoot(domNode);
root.render(
  <AppProvider>
    <App />
  </AppProvider>
)
