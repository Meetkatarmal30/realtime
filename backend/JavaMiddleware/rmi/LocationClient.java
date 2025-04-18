package rmi;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.rmi.Naming;
import org.json.JSONArray;
import org.json.JSONObject;

public class LocationClient {
    public static void main(String[] args) {
        try {
            LocationInterface server = (LocationInterface) Naming.lookup("rmi://localhost/LocationService");

            // STEP 1: Fetch all active trips
            String tripsJson = fetchFromPHP("http://localhost/RealTimeTrackerProject/backend/api/getAllActiveTrips.php");
            JSONArray trips = new JSONArray(tripsJson);

            System.out.println("Active trips fetched:");
            for (int i = 0; i < trips.length(); i++) {
                JSONObject trip = trips.getJSONObject(i);
                int tripId = trip.getInt("trip_id");
                System.out.println(" - Trip ID: " + tripId);
            }

            for (int i = 0; i < trips.length(); i++) {
                JSONObject trip = trips.getJSONObject(i);
                int tripId = trip.getInt("trip_id");

                System.out.println("📍 Trip ID: " + tripId + " - Starting route simulation...");

                // STEP 2: Fetch route points for the trip
                String routeJson = fetchFromPHP("http://localhost/RealTimeTrackerProject/backend/api/getRoutePoints.php?trip_id=" + tripId);
                JSONArray route = new JSONArray(routeJson);

                for (int j = 0; j < route.length(); j++) {
                    JSONObject point = route.getJSONObject(j);
                    double lat = point.getDouble("latitude");
                    double lng = point.getDouble("longitude");

                    String response = server.sendLocation(tripId, lat, lng);
                    System.out.println("➡️ Sent: Trip " + tripId + " → " + lat + ", " + lng + " → " + response);

                    Thread.sleep(3000); // Simulate 3s movement delay
                }

                System.out.println("✅ Finished Trip " + tripId + " simulation.\n");
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private static String fetchFromPHP(String urlStr) throws Exception {
        StringBuilder result = new StringBuilder();
        URL url = new URL(urlStr);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();

        conn.setRequestMethod("GET");
        BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));

        String line;
        while ((line = rd.readLine()) != null) {
            result.append(line);
        }
        rd.close();

        return result.toString();
    }
}