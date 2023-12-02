import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Login from './components/Login/Login';
import Dashboard from './components/Dashboard/Dashboard';
import "App.css";

function App() {
    return (
        <Router basename="/electionlabonline/ELOReact">
            <Routes>
                <Route path="/" element={<Login />} />
                <Route path="/dashboard" element={<Dashboard />} />
                {/* other routes */}
            </Routes>
        </Router>
    );
}
export default App;
