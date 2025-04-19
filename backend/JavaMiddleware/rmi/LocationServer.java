package rmi;

import java.rmi.Naming;
import java.rmi.RemoteException;
import java.rmi.server.UnicastRemoteObject;
import java.net.HttpURLConnection;
import java.net.URL;
import java.io.OutputStream;

public class LocationServer extends UnicastRemoteObject implements LocationInterface {

    protected LocationServer() throws RemoteException {
        super();
    }

    @Override
    public String sendLocation(int tripId, double latitude, double longitude) throws RemoteException {
        try {
            URL url = new URL("http://localhost/RealTimeTrackerProject/backend/api/rmiInsert.php");
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setDoOutput(true);

            String data = "trip_id=" + tripId + "&lat=" + latitude + "&lng=" + longitude;

            OutputStream os = conn.getOutputStream();
            os.write(data.getBytes());
            os.flush();
            os.close();

            int responseCode = conn.getResponseCode();
            return "Location sent for Trip " + tripId + ". Response code: " + responseCode;

        } catch (Exception e) {
            e.printStackTrace();
            return "Error: " + e.getMessage();
        }
    }

    public static void main(String[] args) {
        try {
            Naming.rebind("LocationService", new LocationServer());
            System.out.println("RMI Server is running...");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
