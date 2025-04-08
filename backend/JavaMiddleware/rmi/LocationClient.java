package rmi;

import java.rmi.Naming;

public class LocationClient {
    public static void main(String[] args) {
        try {
            LocationInterface server = (LocationInterface) Naming.lookup("rmi://localhost/LocationService");

            // Simulate random coordinates around a base
            double lat = 12.961 + Math.random() * 0.002;
            double lng = 77.641 + Math.random() * 0.002;

            String response = server.sendLocation(lat, lng);
            System.out.println("Server response: " + response);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
