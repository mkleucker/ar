package edu.dhbw.andobjviewer;

import android.app.ProgressDialog;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.Bitmap.CompressFormat;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Debug;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.SurfaceHolder;
import android.widget.Toast;
import edu.dhbw.andar.ARToolkit;
import edu.dhbw.andar.AndARActivity;
import edu.dhbw.andar.exceptions.AndARException;
import edu.dhbw.andarmodelviewer.R;
import edu.dhbw.andobjviewer.graphics.LightingRenderer;
import edu.dhbw.andobjviewer.graphics.Model3D;
import edu.dhbw.andobjviewer.models.Model;
import edu.dhbw.andobjviewer.parser.ObjParser;
import edu.dhbw.andobjviewer.parser.ParseException;
import edu.dhbw.andobjviewer.util.AssetsFileUtil;
import edu.dhbw.andobjviewer.util.BaseFileUtil;

import java.io.*;
import java.util.ArrayList;
import java.util.Date;

/**
 * Example of an application that makes use of the AndAR toolkit.
 *
 * @author Tobi
 */
public class AugmentedModelViewerActivity extends AndARActivity implements SurfaceHolder.Callback {

	/**
	 * View a file in the assets folder
	 */
	public static final int TYPE_INTERNAL = 0;
	/**
	 * View a file on the sd card.
	 */
	public static final int TYPE_EXTERNAL = 1;

	public static final boolean DEBUG = false;

	/* Menu Options: */
	private final int MENU_SCALE = 0;
	private final int MENU_ROTATE = 1;
	private final int MENU_TRANSLATE = 2;
	private final int MENU_SCREENSHOT = 3;

	private int mode = MENU_SCALE;


	private ArrayList<Model> models;
	private ArrayList<Model3D> models3d;
	private ProgressDialog waitDialog;
	private Resources res;
	private LightingRenderer renderer;

	ARToolkit artoolkit;

	public AugmentedModelViewerActivity() {
		super(false);

		models = new ArrayList<Model>();
		models3d = new ArrayList<Model3D>();
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		renderer = new LightingRenderer();
		super.setNonARRenderer(renderer);//or might be omited
		res = getResources();
		artoolkit = getArtoolkit();
	}


	/**
	 * Inform the user about exceptions that occurred in background threads.
	 */
	@Override
	public void uncaughtException(Thread thread, Throwable ex) {
		Log.e(AugmentedModelViewerActivity.class.getSimpleName(), ex.getMessage(), ex);
	}


	/* create the menu
	 * @see android.app.Activity#onCreateOptionsMenu(android.view.Menu)
	 */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		menu.add(0, MENU_TRANSLATE, 0, res.getText(R.string.translate))
			.setIcon(R.drawable.translate);
		menu.add(0, MENU_ROTATE, 0, res.getText(R.string.rotate))
			.setIcon(R.drawable.rotate);
		menu.add(0, MENU_SCALE, 0, res.getText(R.string.scale))
			.setIcon(R.drawable.scale);
		menu.add(0, MENU_SCREENSHOT, 0, res.getText(R.string.take_screenshot))
			.setIcon(R.drawable.screenshoticon);
		return true;
	}

	/* Handles item selections */
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
			case MENU_SCALE:
				mode = MENU_SCALE;
				return true;
			case MENU_ROTATE:
				mode = MENU_ROTATE;
				return true;
			case MENU_TRANSLATE:
				mode = MENU_TRANSLATE;
				return true;
			case MENU_SCREENSHOT:
				new TakeAsyncScreenshot().execute();
				return true;
		}
		return false;
	}

	@Override
	public void surfaceCreated(SurfaceHolder holder) {
		super.surfaceCreated(holder);
		//load the model
		//this is done here, to assure the surface was already created, so that the preview can be started
		//after loading the model
		if (models.size() == 0) {
			waitDialog = ProgressDialog.show(this, "",
					getResources().getText(R.string.loading), true);
			waitDialog.show();
			ArrayList<String> models = new ArrayList<String>();
			//models.add("android");
			//models.add("barcode");
			models.add("sofa");
			models.add("chair");
			models.add("plant");
			ModelLoader loader = new ModelLoader(models);
			loader.execute();
		}
	}


	private class ModelLoader extends AsyncTask<Void, Void, Void> {

		private ArrayList<String> modelNames;

		public ModelLoader(ArrayList<String> modelNames) {
			this.modelNames = modelNames;
		}

		@Override
		protected Void doInBackground(Void... params) {

			for (String model : this.modelNames) {
				this.getModel(model);
			}

			return null;
		}

		private void getModel(String name) {

			String modelFileName = name + ".obj";
			File modelFile = null;

			BaseFileUtil fileUtil = new AssetsFileUtil(getResources().getAssets());
			fileUtil.setBaseFolder("models/");


			//read the model file:
			if (modelFileName.endsWith(".obj")) {
				ObjParser parser = new ObjParser(fileUtil);
				try {
					if (Config.DEBUG)
						Debug.startMethodTracing("AndObjViewer");

					if (fileUtil != null) {
						BufferedReader fileReader = fileUtil.getReaderFromName(modelFileName);
						if (fileReader != null) {
							Model model = parser.parse("Model", fileReader);
							models.add(model);
							models3d.add(new Model3D(model, name + ".patt"));

						}
					}
					if (Config.DEBUG)
						Debug.stopMethodTracing();
				} catch (IOException e) {
					e.printStackTrace();
				} catch (ParseException e) {
					e.printStackTrace();
				}
			}
		}

		@Override
		protected void onPostExecute(Void result) {
			super.onPostExecute(result);
			waitDialog.dismiss();

			Log.d("asdf", "Dismiss...");
			//register model
			try {
				Log.d("asdf", "try...");
				Log.d("asdf", "try for ..." + models3d.size() + models3d);

				if (models3d.size() > 0) {
					for (Model3D model3d : models3d) {
						Log.d("asdf", "Start add...");
						artoolkit.registerARObject(model3d);
						Log.d("asdf", "Added two...");
					}
					renderer.setModels(models3d);
				}
			} catch (AndARException e) {
				e.printStackTrace();
			}
			startPreview();
		}
	}

	class TakeAsyncScreenshot extends AsyncTask<Void, Void, Void> {

		private String errorMsg = null;

		@Override
		protected Void doInBackground(Void... params) {
			Bitmap bm = takeScreenshot();
			FileOutputStream fos;
			try {
				fos = new FileOutputStream("/sdcard/AndARScreenshot" + new Date().getTime() + ".png");
				bm.compress(CompressFormat.PNG, 100, fos);
				fos.flush();
				fos.close();
			} catch (FileNotFoundException e) {
				errorMsg = e.getMessage();
				e.printStackTrace();
			} catch (IOException e) {
				errorMsg = e.getMessage();
				e.printStackTrace();
			}
			return null;
		}

		protected void onPostExecute(Void result) {
			if (errorMsg == null)
				Toast.makeText(AugmentedModelViewerActivity.this, getResources().getText(R.string.screenshotsaved), Toast.LENGTH_SHORT).show();
			else
				Toast.makeText(AugmentedModelViewerActivity.this, getResources().getText(R.string.screenshotfailed) + errorMsg, Toast.LENGTH_SHORT).show();
		}

		;

	}


}
